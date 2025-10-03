<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Models\ProductUpload;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserTierService
{
    protected $tierConfig = [
        'bronze' => [
            'level' => 1,
            'name' => 'Bronze',
            'multiplier' => 1.0,
            'requirements' => [
                'min_rebates' => 0,
                'min_earnings' => 0,
                'min_uploads' => 0,
                'min_referrals' => 0,
                'account_age_days' => 0
            ],
            'benefits' => [
                'base_rebate_rate' => '1x',
                'priority_processing' => false,
                'special_promotions' => false
            ]
        ],
        'silver' => [
            'level' => 2,
            'name' => 'Silver',
            'multiplier' => 1.2,
            'requirements' => [
                'min_rebates' => 10,
                'min_earnings' => 1000,
                'min_uploads' => 25,
                'min_referrals' => 3,
                'account_age_days' => 30
            ],
            'benefits' => [
                'base_rebate_rate' => '1.2x',
                'priority_processing' => false,
                'special_promotions' => true
            ]
        ],
        'gold' => [
            'level' => 3,
            'name' => 'Gold',
            'multiplier' => 1.5,
            'requirements' => [
                'min_rebates' => 50,
                'min_earnings' => 5000,
                'min_uploads' => 100,
                'min_referrals' => 10,
                'account_age_days' => 90
            ],
            'benefits' => [
                'base_rebate_rate' => '1.5x',
                'priority_processing' => true,
                'special_promotions' => true
            ]
        ],
        'platinum' => [
            'level' => 4,
            'name' => 'Platinum',
            'multiplier' => 2.0,
            'requirements' => [
                'min_rebates' => 150,
                'min_earnings' => 15000,
                'min_uploads' => 300,
                'min_referrals' => 25,
                'account_age_days' => 180
            ],
            'benefits' => [
                'base_rebate_rate' => '2.0x',
                'priority_processing' => true,
                'special_promotions' => true,
                'dedicated_support' => true
            ]
        ],
        'diamond' => [
            'level' => 5,
            'name' => 'Diamond',
            'multiplier' => 2.5,
            'requirements' => [
                'min_rebates' => 300,
                'min_earnings' => 25000,
                'min_uploads' => 500,
                'min_referrals' => 50,
                'account_age_days' => 365
            ],
            'benefits' => [
                'base_rebate_rate' => '2.5x',
                'priority_processing' => true,
                'special_promotions' => true,
                'dedicated_support' => true,
                'exclusive_programs' => true
            ]
        ]
    ];

    /**
     * Get user's current tier
     */
    public function getUserTier(User $user): string
    {
        $cacheKey = "user_tier_{$user->id}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            $userStats = $this->calculateUserStats($user);
            
            // Check tiers from highest to lowest
            foreach (array_reverse($this->tierConfig, true) as $tierName => $config) {
                if ($this->meetsRequirements($userStats, $config['requirements'])) {
                    return $tierName;
                }
            }
            
            return 'bronze'; // Default tier
        });
    }

    /**
     * Get user's tier level (numeric)
     */
    public function getUserTierLevel(User $user): int
    {
        $tier = $this->getUserTier($user);
        return $this->tierConfig[$tier]['level'];
    }

    /**
     * Get tier multiplier for user
     */
    public function getTierMultiplier(User $user): float
    {
        $tier = $this->getUserTier($user);
        return $this->tierConfig[$tier]['multiplier'];
    }

    /**
     * Get tier configuration
     */
    public function getTierConfig(string $tier = null): array
    {
        if ($tier) {
            return $this->tierConfig[$tier] ?? [];
        }
        
        return $this->tierConfig;
    }

    /**
     * Calculate user statistics for tier evaluation
     */
    protected function calculateUserStats(User $user): array
    {
        $accountAge = $user->created_at->diffInDays(now());
        
        // Count approved rebates
        $totalRebates = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        
        // Calculate total earnings
        $totalEarnings = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('rebate_amount');
        
        // Count product uploads
        $totalUploads = ProductUpload::where('user_id', $user->id)->count();
        
        // Count referrals
        $totalReferrals = User::where('ref_by', $user->id)->count();
        
        // Calculate recent activity (last 90 days)
        $recentActivity = [
            'rebates' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('created_at', '>=', now()->subDays(90))
                ->count(),
            'uploads' => ProductUpload::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(90))
                ->count(),
            'transactions' => Transaction::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(90))
                ->count()
        ];
        
        return [
            'account_age_days' => $accountAge,
            'total_rebates' => $totalRebates,
            'total_earnings' => $totalEarnings,
            'total_uploads' => $totalUploads,
            'total_referrals' => $totalReferrals,
            'recent_activity' => $recentActivity,
            'success_rate' => $this->calculateSuccessRate($user)
        ];
    }

    /**
     * Check if user meets tier requirements
     */
    protected function meetsRequirements(array $userStats, array $requirements): bool
    {
        // Map requirement keys to user stat keys
        $keyMapping = [
            'min_rebates' => 'total_rebates',
            'min_earnings' => 'total_earnings',
            'min_uploads' => 'total_uploads',
            'min_referrals' => 'total_referrals',
            'account_age_days' => 'account_age_days'
        ];
        
        foreach ($requirements as $requirement => $minValue) {
            $statKey = $keyMapping[$requirement] ?? $requirement;
            
            if (!isset($userStats[$statKey]) || $userStats[$statKey] < $minValue) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Calculate user's success rate
     */
    protected function calculateSuccessRate(User $user): float
    {
        $totalSubmissions = \App\Models\RebateTransaction::where('user_id', $user->id)->count();
        
        if ($totalSubmissions === 0) {
            return 0;
        }
        
        $approvedSubmissions = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        
        return round(($approvedSubmissions / $totalSubmissions) * 100, 2);
    }

    /**
     * Get user's progress to next tier
     */
    public function getTierProgress(User $user): array
    {
        $currentTier = $this->getUserTier($user);
        $currentLevel = $this->tierConfig[$currentTier]['level'];
        $userStats = $this->calculateUserStats($user);
        
        // Find next tier
        $nextTier = null;
        foreach ($this->tierConfig as $tierName => $config) {
            if ($config['level'] === $currentLevel + 1) {
                $nextTier = $tierName;
                break;
            }
        }
        
        if (!$nextTier) {
            return [
                'current_tier' => $currentTier,
                'next_tier' => null,
                'progress_percentage' => 100,
                'requirements_met' => [],
                'requirements_needed' => []
            ];
        }
        
        $nextRequirements = $this->tierConfig[$nextTier]['requirements'];
        $requirementsMet = [];
        $requirementsNeeded = [];
        $totalProgress = 0;
        
        foreach ($nextRequirements as $requirement => $needed) {
            $current = $userStats[$requirement];
            $progress = min(($current / $needed) * 100, 100);
            $totalProgress += $progress;
            
            if ($current >= $needed) {
                $requirementsMet[] = [
                    'requirement' => $requirement,
                    'current' => $current,
                    'needed' => $needed,
                    'progress' => 100
                ];
            } else {
                $requirementsNeeded[] = [
                    'requirement' => $requirement,
                    'current' => $current,
                    'needed' => $needed,
                    'remaining' => $needed - $current,
                    'progress' => $progress
                ];
            }
        }
        
        return [
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'progress_percentage' => round($totalProgress / count($nextRequirements), 2),
            'requirements_met' => $requirementsMet,
            'requirements_needed' => $requirementsNeeded
        ];
    }

    /**
     * Get all users in a specific tier
     */
    public function getUsersByTier(string $tier): \Illuminate\Database\Eloquent\Collection
    {
        // This is expensive, so use with pagination in production
        return User::active()->get()->filter(function ($user) use ($tier) {
            return $this->getUserTier($user) === $tier;
        });
    }

    /**
     * Upgrade user tier if eligible
     */
    public function checkAndUpgradeTier(User $user): array
    {
        $oldTier = $this->getUserTier($user);
        
        // Clear cache to recalculate
        $cacheKey = "user_tier_{$user->id}";
        Cache::forget($cacheKey);
        
        $newTier = $this->getUserTier($user);
        
        $upgraded = $oldTier !== $newTier;
        
        if ($upgraded) {
            // Log tier upgrade
            $this->logTierUpgrade($user, $oldTier, $newTier);
            
            // Send notification (implement as needed)
            $this->sendTierUpgradeNotification($user, $newTier);
        }
        
        return [
            'upgraded' => $upgraded,
            'old_tier' => $oldTier,
            'new_tier' => $newTier,
            'new_multiplier' => $this->getTierMultiplier($user)
        ];
    }

    /**
     * Get tier statistics for admin dashboard
     */
    public function getTierStatistics(): array
    {
        $stats = [];
        
        foreach ($this->tierConfig as $tierName => $config) {
            // This is expensive - in production, maintain tier counts in a separate table
            $userCount = User::active()->get()->filter(function ($user) use ($tierName) {
                return $this->getUserTier($user) === $tierName;
            })->count();
            
            $stats[$tierName] = [
                'name' => $config['name'],
                'level' => $config['level'],
                'multiplier' => $config['multiplier'],
                'user_count' => $userCount
            ];
        }
        
        return $stats;
    }

    /**
     * Calculate potential tier benefits for user
     */
    public function calculateTierBenefits(User $user, string $targetTier = null): array
    {
        $currentTier = $this->getUserTier($user);
        $targetTier = $targetTier ?? $currentTier;
        
        $currentMultiplier = $this->tierConfig[$currentTier]['multiplier'];
        $targetMultiplier = $this->tierConfig[$targetTier]['multiplier'];
        
        // Calculate potential earnings increase
        $recentEarnings = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('rebate_amount');
        
        $potentialIncrease = $recentEarnings * ($targetMultiplier - $currentMultiplier);
        
        return [
            'current_tier' => $currentTier,
            'current_multiplier' => $currentMultiplier,
            'target_tier' => $targetTier,
            'target_multiplier' => $targetMultiplier,
            'potential_monthly_increase' => $potentialIncrease,
            'benefits' => $this->tierConfig[$targetTier]['benefits']
        ];
    }

    /**
     * Log tier upgrade
     */
    protected function logTierUpgrade(User $user, string $oldTier, string $newTier): void
    {
        // Log to database or external service
        Log::info('User tier upgraded', [
            'user_id' => $user->id,
            'username' => $user->username,
            'old_tier' => $oldTier,
            'new_tier' => $newTier,
            'timestamp' => now()
        ]);
    }

    /**
     * Send tier upgrade notification
     */
    protected function sendTierUpgradeNotification(User $user, string $newTier): void
    {
        // Implement notification logic (email, in-app, push notification)
        $tierConfig = $this->tierConfig[$newTier];
        
        // This would typically use Laravel's notification system
        // $user->notify(new TierUpgradeNotification($newTier, $tierConfig));
    }

    /**
     * Recalculate all user tiers (admin function)
     */
    public function recalculateAllTiers(): array
    {
        $users = User::active()->get();
        $upgrades = 0;
        $downgrades = 0;
        
        foreach ($users as $user) {
            $result = $this->checkAndUpgradeTier($user);
            
            if ($result['upgraded']) {
                $oldLevel = $this->tierConfig[$result['old_tier']]['level'];
                $newLevel = $this->tierConfig[$result['new_tier']]['level'];
                
                if ($newLevel > $oldLevel) {
                    $upgrades++;
                } else {
                    $downgrades++;
                }
            }
        }
        
        return [
            'total_users' => $users->count(),
            'upgrades' => $upgrades,
            'downgrades' => $downgrades
        ];
    }
}