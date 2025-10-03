<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'rebate_program_id',
        'rebate_category_id',
        'receipt_image',
        'purchase_amount',
        'purchase_date',
        'store_name',
        'description',
        'submission_ip',
        'user_agent',
        'status',
        'rebate_transaction_id',
        'product_name',
        'amount',
        'quantity',
        'image_path',
        'image_thumbnail_path',
        'calculated_rebate',
        'admin_rebate_override',
        'final_rebate_amount',
        'admin_notes',
        'rejection_reason',
        'file_hash',
        'metadata',
        'ip_address',
        'verified_at',
        'rewarded_at',
        'verified_by'
    ];

    protected $casts = [
        'purchase_amount' => 'decimal:2',
        'purchase_date' => 'date',
        'amount' => 'decimal:2',
        'calculated_rebate' => 'decimal:2',
        'admin_rebate_override' => 'decimal:2',
        'final_rebate_amount' => 'decimal:2',
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'rewarded_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rebateProgram()
    {
        return $this->belongsTo(RebateProgram::class, 'rebate_program_id');
    }

    public function category()
    {
        return $this->belongsTo(RebateCategory::class, 'rebate_category_id');
    }

    public function rebateTransaction()
    {
        return $this->belongsTo(\App\Models\RebateTransaction::class, 'rebate_transaction_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRewarded($query)
    {
        return $query->where('status', 'rewarded');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function approve($adminId, $notes = null)
    {
        $this->status = 'approved';
        $this->verified_by = $adminId;
        $this->verified_at = now();
        if ($notes) {
            $this->admin_notes = $notes;
        }
        $this->save();
        return $this;
    }

    public function reject($adminId, $reason, $notes = null)
    {
        $this->status = 'rejected';
        $this->verified_by = $adminId;
        $this->verified_at = now();
        $this->rejection_reason = $reason;
        if ($notes) {
            $this->admin_notes = $notes;
        }
        $this->save();
        return $this;
    }

    public function reward($amount = null)
    {
        $rewardAmount = $amount ?? $this->admin_rebate_override ?? $this->calculated_rebate;
        
        $this->final_rebate_amount = $rewardAmount;
        $this->status = 'rewarded';
        $this->rewarded_at = now();
        $this->save();

        // Create rebate transaction
        RebateTransaction::create([
            'user_id' => $this->user_id,
            'rebate_category_id' => $this->rebate_category_id,
            'transaction_type' => 'product_upload',
            'reference_id' => $this->id,
            'reference_type' => self::class,
            'original_amount' => $this->amount * $this->quantity,
            'rebate_amount' => $rewardAmount,
            'final_amount' => $rewardAmount,
            'status' => 'processed',
            'description' => "Rebate for product: {$this->product_name}",
            'ip_address' => $this->ip_address,
            'processed_at' => now()
        ]);

        // Update user rebate balance
        $userRebate = UserRebate::firstOrCreate(['user_id' => $this->user_id]);
        $userRebate->addEarnings($rewardAmount, "Product rebate: {$this->product_name}");

        return $this;
    }

    public function getImageUrl()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function getThumbnailUrl()
    {
        return $this->image_thumbnail_path ? Storage::url($this->image_thumbnail_path) : null;
    }

    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'rewarded' => 'green',
            default => 'gray'
        };
    }

    public function getTotalAmount()
    {
        return $this->amount * $this->quantity;
    }
}
