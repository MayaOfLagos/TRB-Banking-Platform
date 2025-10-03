<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RebateCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rebate_program_id',
        'name',
        'code',
        'description',
        'rebate_rate',
        'minimum_amount',
        'maximum_rebate',
        'daily_transaction_limit',
        'daily_rebate_limit',
        'tier_multipliers',
        'settings',
        'is_active'
    ];

    protected $casts = [
        'rebate_rate' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_rebate' => 'decimal:2',
        'daily_rebate_limit' => 'decimal:2',
        'tier_multipliers' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function program()
    {
        return $this->belongsTo(RebateProgram::class, 'rebate_program_id');
    }

    public function transactions()
    {
        return $this->hasMany(RebateTransaction::class);
    }

    public function userRebates()
    {
        return $this->hasMany(UserRebate::class);
    }

    public function productUploads()
    {
        return $this->hasMany(ProductUpload::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Helper methods
    public function calculateRebate($amount, $userTier = 1)
    {
        if ($amount < $this->minimum_amount) {
            return 0;
        }

        $baseRebate = $amount * ($this->rebate_rate / 100);
        
        // Apply tier multiplier
        $multipliers = $this->tier_multipliers ?? [];
        $multiplier = $multipliers[$userTier] ?? 1.0;
        
        $finalRebate = $baseRebate * $multiplier;
        
        // Apply maximum rebate limit
        if ($this->maximum_rebate && $finalRebate > $this->maximum_rebate) {
            $finalRebate = $this->maximum_rebate;
        }

        return round($finalRebate, 2);
    }

    public function getTierMultiplier($tier)
    {
        $multipliers = $this->tier_multipliers ?? [];
        return $multipliers[$tier] ?? 1.0;
    }
}
