<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RebateProgramController extends Controller
{
    /**
     * Display rebate programs
     */
    public function index()
    {
        $pageTitle = 'Rebate Programs';
        
        $programs = RebateProgram::with('rebateCategory')
            ->orderBy('created_at', 'desc')
            ->paginate(getPaginate());

        return view('admin.rebate.programs.index', compact('pageTitle', 'programs'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $pageTitle = 'Create Rebate Program';
        $categories = RebateCategory::active()->get();

        return view('admin.rebate.programs.create', compact('pageTitle', 'categories'));
    }

    /**
     * Store new rebate program
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rebate_programs,name',
            'description' => 'nullable|string|max:1000',
            'default_rate' => 'required|numeric|min:0|max:100',
            'minimum_amount' => 'required|numeric|min:0',
            'maximum_rebate' => 'nullable|numeric|min:0',
            'daily_limit' => 'nullable|numeric|min:0',
            'monthly_limit' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:rebate_categories,id',
            'new_categories' => 'nullable|array',
            'new_categories.*.name' => 'required_with:new_categories|string|max:255',
            'new_categories.*.rebate_rate' => 'required_with:new_categories|numeric|min:0|max:100'
        ]);

        try {
            $program = RebateProgram::create([
                'name' => $request->name,
                'description' => $request->description,
                'default_rate' => $request->default_rate,
                'minimum_amount' => $request->minimum_amount,
                'maximum_rebate' => $request->maximum_rebate,
                'daily_limit' => $request->daily_limit,
                'monthly_limit' => $request->monthly_limit,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'is_active' => $request->has('is_active'),
                'settings' => json_encode([
                    'auto_approval' => $request->boolean('auto_approval'),
                    'require_receipt' => $request->boolean('require_receipt'),
                ])
            ]);

            // Handle existing categories
            if ($request->categories) {
                foreach ($request->categories as $categoryId) {
                    $category = RebateCategory::find($categoryId);
                    if ($category) {
                        $category->update(['rebate_program_id' => $program->id]);
                    }
                }
            }

            // Handle new categories
            if ($request->new_categories) {
                foreach ($request->new_categories as $newCategory) {
                    if (!empty($newCategory['name'])) {
                        RebateCategory::create([
                            'rebate_program_id' => $program->id,
                            'name' => $newCategory['name'],
                            'code' => strtoupper(str_replace(' ', '_', $newCategory['name'])),
                            'rebate_rate' => $newCategory['rebate_rate'] ?? $program->default_rate,
                            'minimum_amount' => $program->minimum_amount,
                            'is_active' => true
                        ]);
                    }
                }
            }

            $notify[] = ['success', 'Rebate program created successfully'];
            return redirect()->route('admin.rebate.programs.index')->withNotify($notify);

        } catch (\Exception $e) {
            Log::error('Rebate program creation error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while creating the program'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Show rebate program details
     */
    public function show($id)
    {
        $pageTitle = 'Rebate Program Details';
        $program = RebateProgram::with(['rebateCategory', 'userRebates' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        // Get program statistics
        $stats = [
            'total_rebates' => $program->userRebates()->count(),
            'approved_rebates' => $program->userRebates()->where('status', 'approved')->count(),
            'pending_rebates' => $program->userRebates()->where('status', 'pending')->count(),
            'rejected_rebates' => $program->userRebates()->where('status', 'rejected')->count(),
            'total_amount_paid' => $program->userRebates()->where('status', 'approved')->sum('rebate_amount'),
            'unique_users' => $program->userRebates()->distinct('user_id')->count('user_id')
        ];

        return view('admin.rebate.programs.show', compact('pageTitle', 'program', 'stats'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $pageTitle = 'Edit Rebate Program';
        $program = RebateProgram::findOrFail($id);
        $categories = RebateCategory::active()->get();

        return view('admin.rebate.programs.edit', compact('pageTitle', 'program', 'categories'));
    }

    /**
     * Update rebate program
     */
    public function update(Request $request, $id)
    {
        $program = RebateProgram::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:rebate_programs,name,' . $id,
            'description' => 'required|string|max:1000',
            'rebate_category_id' => 'required|exists:rebate_categories,id',
            'type' => 'required|in:product_upload,referral,loyalty,bonus',
            'fixed_amount' => 'required_if:calculation_method,fixed|numeric|min:0',
            'percentage_rate' => 'required_if:calculation_method,percentage|numeric|min:0|max:100',
            'calculation_method' => 'required|in:fixed,percentage',
            'max_rebates_per_user' => 'nullable|integer|min:0',
            'max_total_rebates' => 'nullable|integer|min:0',
            'minimum_tier' => 'nullable|integer|min:1|max:5',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        try {
            $program->update([
                'name' => $request->name,
                'description' => $request->description,
                'rebate_category_id' => $request->rebate_category_id,
                'type' => $request->type,
                'fixed_amount' => $request->calculation_method === 'fixed' ? $request->fixed_amount : 0,
                'percentage_rate' => $request->calculation_method === 'percentage' ? $request->percentage_rate : 0,
                'max_rebates_per_user' => $request->max_rebates_per_user,
                'max_total_rebates' => $request->max_total_rebates,
                'minimum_tier' => $request->minimum_tier,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->has('is_active'),
                'updated_by' => auth()->guard('admin')->id()
            ]);

            $notify[] = ['success', 'Rebate program updated successfully'];
            return redirect()->route('admin.rebate.programs.index')->withNotify($notify);

        } catch (\Exception $e) {
            Log::error('Rebate program update error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while updating the program'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Toggle program status
     */
    public function toggleStatus($id)
    {
        $program = RebateProgram::findOrFail($id);
        
        try {
            $program->update([
                'is_active' => !$program->is_active,
                'updated_by' => auth()->guard('admin')->id()
            ]);

            $status = $program->is_active ? 'activated' : 'deactivated';
            $notify[] = ['success', "Rebate program {$status} successfully"];

        } catch (\Exception $e) {
            Log::error('Program status toggle error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while updating the program status'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Delete rebate program
     */
    public function destroy($id)
    {
        $program = RebateProgram::findOrFail($id);

        // Check if program has associated rebates
        if ($program->userRebates()->exists()) {
            $notify[] = ['error', 'Cannot delete program with existing rebates'];
            return back()->withNotify($notify);
        }

        try {
            $program->delete();
            $notify[] = ['success', 'Rebate program deleted successfully'];

        } catch (\Exception $e) {
            Log::error('Program deletion error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while deleting the program'];
        }

        return redirect()->route('admin.rebate.programs.index')->withNotify($notify);
    }

    /**
     * Duplicate rebate program
     */
    public function duplicate($id)
    {
        $original = RebateProgram::findOrFail($id);

        try {
            $duplicate = $original->replicate();
            $duplicate->name = $original->name . ' (Copy)';
            $duplicate->is_active = false;
            $duplicate->created_by = auth()->guard('admin')->id();
            $duplicate->updated_by = null;
            $duplicate->save();

            $notify[] = ['success', 'Rebate program duplicated successfully'];
            return redirect()->route('admin.rebate.programs.edit', $duplicate->id)->withNotify($notify);

        } catch (\Exception $e) {
            Log::error('Program duplication error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while duplicating the program'];
            return back()->withNotify($notify);
        }
    }
}
