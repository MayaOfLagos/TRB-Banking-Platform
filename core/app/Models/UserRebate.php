<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRebate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_earned',
        'current_balance',
        'total_redeemed',
        'pending_amount',
        'current_tier',
        'last_earned_at',
        'last_redeemed_at',
        'tier_history',
        'statistics'
    ];

    protected $casts = [
        'total_earned' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_redeemed' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'last_earned_at' => 'datetime',
        'last_redeemed_at' => 'datetime',
        'tier_history' => 'array',
        'statistics' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(RebateTransaction::class, 'user_id', 'user_id');
    }

    // Helper methods
    public function addEarnings($amount, $description = null)
    {
        $this->total_earned += $amount;
        $this->current_balance += $amount;
        $this->last_earned_at = now();
        
        // Update statistics
        $stats = $this->statistics ?? [];
        $stats['total_transactions'] = ($stats['total_transactions'] ?? 0) + 1;
        $stats['monthly_earned'] = ($stats['monthly_earned'] ?? 0) + $amount;
        $this->statistics = $stats;
        
        $this->save();
        return $this;
    }

    public function deductBalance($amount, $description = null)
    {
        if ($this->current_balance >= $amount) {
            $this->current_balance -= $amount;
            $this->total_redeemed += $amount;
            $this->last_redeemed_at = now();
            $this->save();
            return true;
        }
        return false;
    }

    public function updateTier()
    {
        $newTier = $this->calculateTier();
        if ($newTier != $this->current_tier) {
            $history = $this->tier_history ?? [];
            $history[] = [
                'from_tier' => $this->current_tier,
                'to_tier' => $newTier,
                'date' => now()->toDateTimeString(),
                'total_earned' => $this->total_earned
            ];
            $this->tier_history = $history;
            $this->current_tier = $newTier;
            $this->save();
        }
        return $this;
    }

    private function calculateTier()
    {
        // Tier calculation based on total earned
        if ($this->total_earned >= 10000) return 4; // Platinum
        if ($this->total_earned >= 5000) return 3;  // Gold
        if ($this->total_earned >= 1000) return 2;  // Silver
        return 1; // Bronze
    }

    public function getTierName()
    {
        $tiers = [1 => 'Bronze', 2 => 'Silver', 3 => 'Gold', 4 => 'Platinum'];
        return $tiers[$this->current_tier] ?? 'Bronze';
    }
}
