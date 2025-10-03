<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'default_rate',
        'minimum_amount',
        'maximum_rebate',
        'daily_limit',
        'monthly_limit',
        'manual_members_count',
        'settings',
        'starts_at',
        'ends_at'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'default_rate' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_rebate' => 'decimal:2',
        'daily_limit' => 'decimal:2',
        'monthly_limit' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime'
    ];

    // Relationships
    public function categories()
    {
        return $this->hasMany(RebateCategory::class);
    }

    public function rebateCategory()
    {
        return $this->hasOne(RebateCategory::class)->oldest();
    }

    public function rebateTransactions()
    {
        return $this->hasMany(RebateTransaction::class, 'rebate_program_id');
    }

    // Legacy alias for backward compatibility
    public function transactions()
    {
        return $this->rebateTransactions();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                    });
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active && 
               (is_null($this->starts_at) || $this->starts_at <= now()) &&
               (is_null($this->ends_at) || $this->ends_at >= now());
    }

    public function getActiveCategories()
    {
        return $this->categories()->where('is_active', true);
    }

    /**
     * Get effective member count - manual override or system count
     */
    public function getEffectiveMembersCount()
    {
        // If manual count is set, use it (even if 0)
        if (!is_null($this->manual_members_count)) {
            return $this->manual_members_count;
        }
        
        // Otherwise, use system count (active users)
        return \App\Models\User::where('status', 1)->count();
    }

    /**
     * Get system member count (for display purposes)
     */
    public function getSystemMembersCount()
    {
        return \App\Models\User::where('status', 1)->count();
    }

    /**
     * Check if using manual member count override
     */
    public function isUsingManualMembersCount()
    {
        return !is_null($this->manual_members_count);
    }
}
