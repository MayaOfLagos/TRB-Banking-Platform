<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    /**
     * Display file manager
     */
    public function index()
    {
        $pageTitle = 'File Manager';
        $files = FileManager::with('uploader')->latest()->paginate(getPaginate(20));
        
        return view('admin.file_manager.index', compact('pageTitle', 'files'));
    }

    /**
     * Upload files
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'required|file|max:10240', // Max 10MB
        ]);

        $uploadedFiles = [];
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $fileName = pathinfo($originalName, PATHINFO_FILENAME);
                    $fileName = Str::slug($fileName) . '_' . time() . rand(1000, 9999) . '.' . $extension;
                    
                    // Get file size BEFORE moving (important!)
                    $fileSize = $file->getSize();
                    
                    // Determine directory based on file type
                    $directory = 'assets/images/uploaded/';
                    if (in_array(strtolower($extension), ['pdf', 'doc', 'docx', 'xls', 'xlsx'])) {
                        $directory = 'assets/documents/uploaded/';
                    }

                    // Create directory if it doesn't exist
                    // Use base_path() to get the root directory (c:\xampp\htdocs)
                    $fullPath = base_path('../' . $directory);
                    if (!File::exists($fullPath)) {
                        File::makeDirectory($fullPath, 0755, true);
                    }

                    // Move file
                    $file->move($fullPath, $fileName);
                    
                    // Save to database
                    $fileManager = FileManager::create([
                        'name' => $originalName,
                        'path' => $directory . $fileName,
                        'type' => $extension,
                        'size' => $fileSize,
                        'uploaded_by' => auth()->guard('admin')->id(),
                    ]);

                    $uploadedFiles[] = $fileManager;
                } catch (\Exception $e) {
                    $notify[] = ['error', 'Error uploading ' . $originalName . ': ' . $e->getMessage()];
                }
            }
        }

        if (count($uploadedFiles) > 0) {
            $notify[] = ['success', count($uploadedFiles) . ' file(s) uploaded successfully'];
        }

        return back()->withNotify($notify ?? []);
    }

    /**
     * Delete single file
     */
    public function delete($id)
    {
        $file = FileManager::findOrFail($id);
        $file->delete();

        $notify[] = ['success', 'File deleted successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Bulk delete files
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:file_managers,id',
        ]);

        $files = FileManager::whereIn('id', $request->ids)->get();
        
        foreach ($files as $file) {
            $file->delete();
        }

        $notify[] = ['success', count($files) . ' file(s) deleted successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Search files
     */
    public function search(Request $request)
    {
        $pageTitle = 'File Manager - Search Results';
        $search = $request->search;
        
        $files = FileManager::with('uploader')
            ->where('name', 'like', "%{$search}%")
            ->orWhere('type', 'like', "%{$search}%")
            ->latest()
            ->paginate(getPaginate(20));
        
        return view('admin.file_manager.index', compact('pageTitle', 'files', 'search'));
    }
}
