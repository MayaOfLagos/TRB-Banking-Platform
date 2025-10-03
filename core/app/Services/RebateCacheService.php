<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\RebateProgram;
use App\Models\RebateTransaction;
use App\Models\User;

class RebateCacheService
{
    const CACHE_TTL = 3600; // 1 hour
    
    /**
     * Cache rebate programs with hierarchical structure
     */
    public function cacheRebatePrograms()
    {
        $cacheKey = 'rebate_programs_hierarchy';
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return RebateProgram::where('status', 1)
                ->with(['parent', 'children'])
                ->get()
                ->map(function ($program) {
                    return [
                        'id' => $program->id,
                        'name' => $program->name,
                        'rebate_percentage' => $program->rebate_percentage,
                        'minimum_amount' => $program->minimum_amount,
                        'maximum_amount' => $program->maximum_amount,
                        'tier' => $program->tier,
                        'parent_id' => $program->parent_id,
                        'children_count' => $program->children->count(),
                        'requirements' => $program->requirements,
                        'description' => $program->description,
                        'valid_from' => $program->valid_from,
                        'valid_until' => $program->valid_until,
                    ];
                });
        });
    }
    
    /**
     * Cache user rebate statistics
     */
    public function cacheUserRebateStats($userId)
    {
        $cacheKey = "user_rebate_stats_{$userId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $user = User::find($userId);
            if (!$user) return null;
            
            // Get user's rebate transactions summary
            $stats = RebateTransaction::where('user_id', $userId)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    SUM(CASE WHEN status = "approved" THEN rebate_amount ELSE 0 END) as total_earned,
                    SUM(CASE WHEN status = "pending" THEN rebate_amount ELSE 0 END) as pending_amount,
                    AVG(CASE WHEN status = "approved" THEN rebate_percentage ELSE NULL END) as avg_rebate_rate,
                    COUNT(CASE WHEN status = "approved" THEN 1 END) as approved_count,
                    COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count,
                    COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected_count
                ')
                ->first();
            
            // Calculate tier advancement metrics
            $currentTier = $this->calculateUserTier($userId);
            $nextTierRequirement = $this->getNextTierRequirement($currentTier);
            
            return [
                'user_id' => $userId,
                'current_tier' => $currentTier,
                'next_tier_requirement' => $nextTierRequirement,
                'total_transactions' => $stats->total_transactions ?? 0,
                'total_earned' => (float) ($stats->total_earned ?? 0),
                'pending_amount' => (float) ($stats->pending_amount ?? 0),
                'average_rebate_rate' => (float) ($stats->avg_rebate_rate ?? 0),
                'approved_count' => $stats->approved_count ?? 0,
                'pending_count' => $stats->pending_count ?? 0,
                'rejected_count' => $stats->rejected_count ?? 0,
                'success_rate' => $stats->total_transactions > 0 
                    ? round(($stats->approved_count / $stats->total_transactions) * 100, 2) 
                    : 0,
            ];
        });
    }
    
    /**
     * Cache rebate program eligibility for user
     */
    public function cacheUserEligibility($userId)
    {
        $cacheKey = "user_eligibility_{$userId}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL / 2, function () use ($userId) {
            $programs = $this->cacheRebatePrograms();
            $userStats = $this->cacheUserRebateStats($userId);
            
            $eligiblePrograms = [];
            
            foreach ($programs as $program) {
                $isEligible = $this->checkProgramEligibility($userId, $program, $userStats);
                
                $eligiblePrograms[] = [
                    'program_id' => $program['id'],
                    'program_name' => $program['name'],
                    'rebate_percentage' => $program['rebate_percentage'],
                    'is_eligible' => $isEligible,
                    'minimum_amount' => $program['minimum_amount'],
                    'maximum_amount' => $program['maximum_amount'],
                    'tier_required' => $program['tier'],
                    'user_current_tier' => $userStats['current_tier'] ?? 1,
                ];
            }
            
            return $eligiblePrograms;
        });
    }
    
    /**
     * Cache admin dashboard metrics
     */
    public function cacheAdminDashboard()
    {
        $cacheKey = 'rebate_admin_dashboard';
        
        return Cache::remember($cacheKey, self::CACHE_TTL / 4, function () {
            $today = now()->startOfDay();
            $thisMonth = now()->startOfMonth();
            $thisYear = now()->startOfYear();
            
            return [
                // Transaction metrics
                'total_transactions' => RebateTransaction::count(),
                'pending_transactions' => RebateTransaction::where('status', 'pending')->count(),
                'approved_today' => RebateTransaction::where('status', 'approved')
                    ->where('created_at', '>=', $today)->count(),
                'total_rebate_paid' => RebateTransaction::where('status', 'approved')
                    ->sum('rebate_amount'),
                
                // Monthly metrics
                'monthly_transactions' => RebateTransaction::where('created_at', '>=', $thisMonth)->count(),
                'monthly_rebate_amount' => RebateTransaction::where('status', 'approved')
                    ->where('created_at', '>=', $thisMonth)->sum('rebate_amount'),
                
                // Yearly metrics
                'yearly_transactions' => RebateTransaction::where('created_at', '>=', $thisYear)->count(),
                'yearly_rebate_amount' => RebateTransaction::where('status', 'approved')
                    ->where('created_at', '>=', $thisYear)->sum('rebate_amount'),
                
                // Program metrics
                'active_programs' => RebateProgram::where('status', 1)->count(),
                'total_programs' => RebateProgram::count(),
                
                // Performance metrics
                'approval_rate' => $this->calculateApprovalRate(),
                'average_processing_time' => $this->calculateAverageProcessingTime(),
                'top_performing_programs' => $this->getTopPerformingPrograms(),
                
                'last_updated' => now()->toISOString(),
            ];
        });
    }
    
    /**
     * Clear specific cache keys
     */
    public function clearUserCache($userId)
    {
        Cache::forget("user_rebate_stats_{$userId}");
        Cache::forget("user_eligibility_{$userId}");
        
        // Clear user-specific transaction caches
        Cache::forget("user_transactions_{$userId}");
    }
    
    /**
     * Clear all rebate-related caches
     */
    public function clearAllCaches()
    {
        $patterns = [
            'rebate_programs_hierarchy',
            'rebate_admin_dashboard',
            'user_rebate_stats_*',
            'user_eligibility_*',
            'user_transactions_*',
        ];
        
        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // For wildcard patterns, we'd need a more sophisticated cache clearing mechanism
                // For now, we'll use cache tags in production
                continue;
            }
            Cache::forget($pattern);
        }
    }
    
    /**
     * Warm up critical caches
     */
    public function warmupCaches()
    {
        // Warm up rebate programs cache
        $this->cacheRebatePrograms();
        
        // Warm up admin dashboard
        $this->cacheAdminDashboard();
        
        // Warm up active user caches (last 100 active users)
        $activeUsers = User::where('status', 1)
            ->whereHas('rebateTransactions')
            ->orderBy('last_login_at', 'desc')
            ->limit(100)
            ->pluck('id');
            
        foreach ($activeUsers as $userId) {
            $this->cacheUserRebateStats($userId);
            $this->cacheUserEligibility($userId);
        }
    }
    
    /**
     * Calculate user's current tier
     */
    private function calculateUserTier($userId)
    {
        $totalEarned = RebateTransaction::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('rebate_amount');
            
        // Tier calculation logic
        if ($totalEarned >= 10000) return 5; // Platinum
        if ($totalEarned >= 5000) return 4;  // Gold  
        if ($totalEarned >= 2000) return 3;  // Silver
        if ($totalEarned >= 500) return 2;   // Bronze
        return 1; // Basic
    }
    
    /**
     * Get next tier requirement
     */
    private function getNextTierRequirement($currentTier)
    {
        $requirements = [
            1 => ['tier' => 2, 'amount' => 500],
            2 => ['tier' => 3, 'amount' => 2000],
            3 => ['tier' => 4, 'amount' => 5000],
            4 => ['tier' => 5, 'amount' => 10000],
            5 => null, // Max tier
        ];
        
        return $requirements[$currentTier] ?? null;
    }
    
    /**
     * Check program eligibility
     */
    private function checkProgramEligibility($userId, $program, $userStats)
    {
        // Check tier requirement
        if ($program['tier'] > ($userStats['current_tier'] ?? 1)) {
            return false;
        }
        
        // Check date validity
        $now = now();
        if ($program['valid_from'] && $now < $program['valid_from']) {
            return false;
        }
        if ($program['valid_until'] && $now > $program['valid_until']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Calculate system-wide approval rate
     */
    private function calculateApprovalRate()
    {
        $total = RebateTransaction::count();
        if ($total === 0) return 0;
        
        $approved = RebateTransaction::where('status', 'approved')->count();
        return round(($approved / $total) * 100, 2);
    }
    
    /**
     * Calculate average processing time
     */
    private function calculateAverageProcessingTime()
    {
        $avgSeconds = RebateTransaction::whereNotNull('processed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, processed_at)) as avg_processing_time')
            ->value('avg_processing_time');
            
        return $avgSeconds ? round($avgSeconds / 3600, 2) : 0; // Convert to hours
    }
    
    /**
     * Get top performing programs
     */
    private function getTopPerformingPrograms()
    {
        return RebateTransaction::join('rebate_programs', 'rebate_transactions.rebate_program_id', '=', 'rebate_programs.id')
            ->where('rebate_transactions.status', 'approved')
            ->groupBy('rebate_programs.id', 'rebate_programs.name')
            ->selectRaw('
                rebate_programs.id,
                rebate_programs.name,
                COUNT(*) as transaction_count,
                SUM(rebate_transactions.rebate_amount) as total_rebate_paid,
                AVG(rebate_transactions.rebate_amount) as avg_rebate_amount
            ')
            ->orderBy('total_rebate_paid', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}