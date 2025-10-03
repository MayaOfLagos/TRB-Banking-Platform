<?php

namespace App\Models;

use App\Events\RebateStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'rebate_category_id',
        'rebate_program_id',
        'product_upload_id',
        'transaction_type',
        'reference_id',
        'reference_type',
        'original_amount',
        'rebate_rate',
        'rebate_amount',
        'tier_multiplier',
        'final_amount',
        'status',
        'description',
        'metadata',
        'ip_address',
        'processed_at'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'rebate_rate' => 'decimal:2',
        'rebate_amount' => 'decimal:2',
        'tier_multiplier' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rebateCategory()
    {
        return $this->belongsTo(RebateCategory::class, 'rebate_category_id');
    }

    public function rebateProgram()
    {
        return $this->belongsTo(RebateProgram::class, 'rebate_program_id');
    }

    // Legacy aliases for backward compatibility
    public function category()
    {
        return $this->rebateCategory();
    }

    public function program()
    {
        return $this->rebateProgram();
    }

    public function product_upload()
    {
        return $this->belongsTo(ProductUpload::class, 'product_upload_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function markAsProcessed()
    {
        $this->status = 'processed';
        $this->processed_at = now();
        $this->save();
        return $this;
    }

    public function markAsFailed($reason = null)
    {
        $this->status = 'failed';
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['failure_reason'] = $reason;
            $this->metadata = $metadata;
        }
        $this->save();
        return $this;
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processed' => 'green',
            'approved' => 'green',
            'rejected' => 'red',
            'failed' => 'red',
            'reversed' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            // Check if status is being changed
            if ($model->isDirty('status')) {
                $previousStatus = $model->getOriginal('status');
                $newStatus = $model->status;
                
                // Dispatch event after the update is saved
                static::updated(function () use ($model, $previousStatus, $newStatus) {
                    event(new RebateStatusChanged($model, $previousStatus, $newStatus));
                });
            }
        });
    }

    /**
     * Approve rebate transaction
     */
    public function approve($approvedBy = null, $notes = null)
    {
        $this->status = 'approved';
        $this->approved_at = now();
        $this->review_notes = $notes;
        if ($approvedBy) {
            $metadata = $this->metadata ?? [];
            $metadata['approved_by'] = $approvedBy;
            $this->metadata = $metadata;
        }
        $this->save();
        return $this;
    }

    /**
     * Reject rebate transaction
     */
    public function reject($rejectedBy = null, $reason = null)
    {
        $this->status = 'rejected';
        $this->rejected_at = now();
        $this->rejected_by = $rejectedBy;
        $this->review_notes = $reason;
        $this->save();
        return $this;
    }
}
