<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBillingCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code_type',
        'code',
        'amount',
        'description',
        'status',
        'is_required',
        'expires_at',
        'used_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'status' => 'integer',
        'is_required' => 'integer',
        'expires_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    // Constants for code types
    const CODE_TYPE_IMF = 'IMF';
    const CODE_TYPE_TAX = 'TAX';
    const CODE_TYPE_COT = 'COT';

    // Constants for status
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    // Constants for requirement
    const REQUIRED = 1;
    const OPTIONAL = 0;

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Active codes only
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Required codes only
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', self::REQUIRED);
    }

    /**
     * Scope: Unused codes only
     */
    public function scopeUnused($query)
    {
        return $query->whereNull('used_at');
    }

    /**
     * Scope: Non-expired codes only
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope: By code type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('code_type', strtoupper($type));
    }

    /**
     * Check if code is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if code is used
     */
    public function isUsed()
    {
        return !is_null($this->used_at);
    }

    /**
     * Mark code as used
     */
    public function markAsUsed()
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Get available code types
     */
    public static function getCodeTypes()
    {
        return [
            self::CODE_TYPE_IMF => 'International Monetary Fund (IMF)',
            self::CODE_TYPE_TAX => 'Tax Clearance Code',
            self::CODE_TYPE_COT => 'Certificate of Transfer (COT)'
        ];
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge()
    {
        if ($this->isUsed()) {
            return '<span class="badge badge--success">Used</span>';
        }

        if ($this->isExpired()) {
            return '<span class="badge badge--danger">Expired</span>';
        }

        if ($this->status == self::STATUS_ACTIVE) {
            return '<span class="badge badge--primary">Active</span>';
        }

        return '<span class="badge badge--warning">Inactive</span>';
    }

    /**
     * Get requirement badge HTML
     */
    public function getRequirementBadge()
    {
        return $this->is_required == self::REQUIRED 
            ? '<span class="badge badge--dark">Required</span>'
            : '<span class="badge badge--info">Optional</span>';
    }

    /**
     * Check if user has pending billing codes
     */
    public static function userHasPendingCodes($userId)
    {
        return self::where('user_id', $userId)
            ->active()
            ->unused()
            ->notExpired()
            ->exists();
    }

    /**
     * Get user's pending required billing codes
     */
    public static function getUserPendingRequiredCodes($userId)
    {
        return self::where('user_id', $userId)
            ->active()
            ->required()
            ->unused()
            ->notExpired()
            ->get();
    }

    /**
     * Get user's total pending amount
     */
    public static function getUserPendingAmount($userId)
    {
        return self::where('user_id', $userId)
            ->active()
            ->required()
            ->unused()
            ->notExpired()
            ->sum('amount');
    }
}