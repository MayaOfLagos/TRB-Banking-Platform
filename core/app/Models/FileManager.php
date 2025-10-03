<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileManager extends Model
{
    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the admin who uploaded the file
     */
    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    /**
     * Get full URL of the file
     */
    public function getUrlAttribute()
    {
        return asset($this->path);
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array(strtolower($this->type), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp']);
    }

    /**
     * Delete file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            $filePath = base_path('../' . $file->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        });
    }
}
