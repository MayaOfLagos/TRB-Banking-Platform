<?php

namespace App\Services;

use App\Models\User;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Models\ProductUpload;
use App\Constants\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RebateCalculatorService
{
    protected $fraudDetectionService;
    protected $tierService;

    public function __construct(
        FraudDetectionService $fraudDetectionService,
        UserTierService $tierService
    ) {
        $this->fraudDetectionService = $fraudDetectionService;
        $this->tierService = $tierService;
    }

    /**
     * Calculate rebate for a product upload
     */
    public function calculateProductRebate(ProductUpload $productUpload): array
    {
        try {
            // Check if rebate system is enabled
            if (!isRebateSystemEnabled()) {
                return [
                    'eligible' => false,
                    'reason' => 'Rebate system is currently disabled'
                ];
            }
            
            // Get active rebate programs for the category
            $programs = RebateProgram::active()
                ->where('id', $productUpload->rebate_program_id)
                ->where(function ($query) {
                    $query->whereNull('ends_at')
                          ->orWhere('ends_at', '>=', now());
                })
                ->get();

            if ($programs->isEmpty()) {
                return [
                    'eligible' => false,
                    'reason' => 'No active rebate programs for this category'
                ];
            }

            // Check user eligibility
            $eligibilityCheck = $this->checkUserEligibility(
                $productUpload->user, 
                $programs->first()
            );

            if (!$eligibilityCheck['eligible']) {
                return $eligibilityCheck;
            }

            // Calculate base rebate amount
            $baseAmount = $this->calculateBaseRebateAmount($productUpload, $programs);

            // Apply tier multiplier
            if (!$productUpload->user) {
                throw new \Exception('Product upload missing user relationship');
            }
            
            $tierMultiplier = $this->tierService->getTierMultiplier($productUpload->user);
            if (!is_numeric($tierMultiplier) || $tierMultiplier <= 0) {
                $tierMultiplier = 1.0; // Default fallback
            }
            
            $finalAmount = $baseAmount * $tierMultiplier;

            // Apply daily/monthly limits
            $limitCheck = $this->checkRebateLimits($productUpload->user, $finalAmount);
            
            if (!$limitCheck['allowed']) {
                return [
                    'eligible' => false,
                    'reason' => $limitCheck['reason'],
                    'calculated_amount' => $finalAmount,
                    'limit_exceeded' => true
                ];
            }

            // Run fraud detection
            try {
                $fraudCheck = $this->fraudDetectionService->validateProductUpload($productUpload);
                
                if (!$fraudCheck['valid']) {
                    return [
                        'eligible' => false,
                        'reason' => 'Failed fraud detection: ' . $fraudCheck['reason'],
                        'fraud_detected' => true
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Fraud detection failed, proceeding with calculation', [
                    'error' => $e->getMessage(),
                    'product_upload_id' => $productUpload->id
                ]);
                // Set default fraud check result to proceed
                $fraudCheck = ['valid' => true, 'score' => 0];
            }

            return [
                'eligible' => true,
                'rebate_amount' => $finalAmount,
                'base_amount' => $baseAmount,
                'tier_multiplier' => $tierMultiplier,
                'program' => $programs->first(),
                'calculation_details' => [
                    'category' => $productUpload->category->name ?? 'Unknown',
                    'user_tier' => $this->tierService->getUserTier($productUpload->user),
                    'fraud_score' => $fraudCheck['score'] ?? 0
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Rebate calculation error: ' . $e->getMessage(), [
                'product_upload_id' => $productUpload->id,
                'user_id' => $productUpload->user_id,
                'rebate_program_id' => $productUpload->rebate_program_id,
                'purchase_amount' => $productUpload->purchase_amount,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'eligible' => false,
                'reason' => 'Calculation error: ' . $e->getMessage(),
                'error' => true,
                'exception_details' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate referral rebate
     */
    public function calculateReferralRebate(User $referrer, User $referee, string $action = 'signup'): array
    {
        try {
            // Get referral rebate program
            $program = RebateProgram::active()
                ->where('type', 'referral')
                ->where('action_trigger', $action)
                ->first();

            if (!$program) {
                return [
                    'eligible' => false,
                    'reason' => 'No active referral program'
                ];
            }

            // Check referrer eligibility
            $eligibilityCheck = $this->checkUserEligibility($referrer, $program);
            
            if (!$eligibilityCheck['eligible']) {
                return $eligibilityCheck;
            }

            // Calculate referral amount based on tier
            $referrerTier = $this->tierService->getUserTier($referrer);
            $baseAmount = 0;
            
            if ($program->default_rate > 0) {
                // For percentage-based referral (if referee makes purchase)
                $baseAmount = $referee->transactions()
                    ->where('trx_type', '+')
                    ->where('created_at', '>=', $referee->created_at)
                    ->sum('amount') * ($program->default_rate / 100);
            }

            $tierMultiplier = $this->tierService->getTierMultiplier($referrer);
            $finalAmount = $baseAmount * $tierMultiplier;

            // Apply referral limits
            $limitCheck = $this->checkReferralLimits($referrer, $finalAmount);
            
            if (!$limitCheck['allowed']) {
                return [
                    'eligible' => false,
                    'reason' => $limitCheck['reason']
                ];
            }

            return [
                'eligible' => true,
                'rebate_amount' => $finalAmount,
                'base_amount' => $baseAmount,
                'tier_multiplier' => $tierMultiplier,
                'program' => $program,
                'referee' => $referee
            ];

        } catch (\Exception $e) {
            Log::error('Referral rebate calculation error: ' . $e->getMessage(), [
                'referrer_id' => $referrer->id,
                'referee_id' => $referee->id
            ]);

            return [
                'eligible' => false,
                'reason' => 'Calculation error occurred'
            ];
        }
    }

    /**
     * Calculate loyalty rebate based on user activity
     */
    public function calculateLoyaltyRebate(User $user, int $months = 1): array
    {
        try {
            $program = RebateProgram::active()
                ->where('type', 'loyalty')
                ->first();

            if (!$program) {
                return [
                    'eligible' => false,
                    'reason' => 'No active loyalty program'
                ];
            }

            // Calculate user activity score
            $activityScore = $this->calculateActivityScore($user, $months);
            
            if ($activityScore < $program->minimum_activity_score) {
                return [
                    'eligible' => false,
                    'reason' => 'Insufficient activity score',
                    'required_score' => $program->minimum_activity_score,
                    'current_score' => $activityScore
                ];
            }

            // Calculate loyalty rebate based on activity
            $baseAmount = 0;
            
            if ($program->default_rate > 0) {
                $monthlyTransactions = $user->transactions()
                    ->where('trx_type', '+')
                    ->where('created_at', '>=', now()->subMonths($months))
                    ->sum('amount');
                    
                $baseAmount = $monthlyTransactions * ($program->default_rate / 100);
            }

            $tierMultiplier = $this->tierService->getTierMultiplier($user);
            $finalAmount = $baseAmount * $tierMultiplier;

            return [
                'eligible' => true,
                'rebate_amount' => $finalAmount,
                'base_amount' => $baseAmount,
                'tier_multiplier' => $tierMultiplier,
                'activity_score' => $activityScore,
                'program' => $program
            ];

        } catch (\Exception $e) {
            Log::error('Loyalty rebate calculation error: ' . $e->getMessage(), [
                'user_id' => $user->id
            ]);

            return [
                'eligible' => false,
                'reason' => 'Calculation error occurred'
            ];
        }
    }

    /**
     * Check user eligibility for rebate program
     */
    protected function checkUserEligibility(User $user, RebateProgram $program): array
    {
        // Check if user is active and verified
        if ($user->status != Status::USER_ACTIVE) {
            return [
                'eligible' => false,
                'reason' => 'User account is not active'
            ];
        }

        if ($user->ev != Status::VERIFIED || $user->sv != Status::VERIFIED) {
            return [
                'eligible' => false,
                'reason' => 'User email or mobile not verified'
            ];
        }

        // Check minimum tier requirement
        if ($program->minimum_tier) {
            $userTier = $this->tierService->getUserTierLevel($user);
            if ($userTier < $program->minimum_tier) {
                return [
                    'eligible' => false,
                    'reason' => 'User tier does not meet minimum requirement'
                ];
            }
        }

        // Check if user has reached program limits
        if ($program->max_rebates_per_user > 0) {
            $userRebateCount = UserRebate::where('user_id', $user->id)
                ->where('rebate_program_id', $program->id)
                ->count();
                
            if ($userRebateCount >= $program->max_rebates_per_user) {
                return [
                    'eligible' => false,
                    'reason' => 'User has reached maximum rebates for this program'
                ];
            }
        }

        return ['eligible' => true];
    }

    /**
     * Calculate base rebate amount
     */
    protected function calculateBaseRebateAmount(ProductUpload $productUpload, $programs): float
    {
        $program = $programs->first();
        
        if (!$program) {
            throw new \Exception('No program found for calculation');
        }
        
        // Use default_rate field (percentage-based calculation)
        if ($program->default_rate > 0 && $productUpload->purchase_amount > 0) {
            $baseAmount = $productUpload->purchase_amount * ($program->default_rate / 100);
            
            // Apply maximum rebate limit if set
            if ($program->maximum_rebate && $baseAmount > $program->maximum_rebate) {
                $baseAmount = $program->maximum_rebate;
            }
            
            return $baseAmount;
        }
        
        return 0;
    }

    /**
     * Check rebate limits for user
     */
    protected function checkRebateLimits(User $user, float $amount): array
    {
        $dailyLimit = getRebateDailyLimit();
        $monthlyLimit = getRebateMonthlyLimit();
        
        // Check daily limit
        if ($dailyLimit > 0) {
            $todayEarnings = RebateTransaction::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereDate('created_at', today())
                ->sum('final_amount');
                
            if (($todayEarnings + $amount) > $dailyLimit) {
                return [
                    'allowed' => false,
                    'reason' => 'Daily rebate limit exceeded',
                    'daily_limit' => $dailyLimit,
                    'current_earnings' => $todayEarnings
                ];
            }
        }
        
        // Check monthly limit
        if ($monthlyLimit > 0) {
            $monthlyEarnings = RebateTransaction::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('final_amount');
                
            if (($monthlyEarnings + $amount) > $monthlyLimit) {
                return [
                    'allowed' => false,
                    'reason' => 'Monthly rebate limit exceeded',
                    'monthly_limit' => $monthlyLimit,
                    'current_earnings' => $monthlyEarnings
                ];
            }
        }
        
        return ['allowed' => true];
    }

    /**
     * Check referral limits
     */
    protected function checkReferralLimits(User $user, float $amount): array
    {
        $general = gs();
        
        if ($general->referral_daily_limit > 0) {
            $todayReferrals = UserRebate::where('user_id', $user->id)
                ->where('type', 'referral')
                ->whereDate('created_at', today())
                ->sum('rebate_amount');
                
            if (($todayReferrals + $amount) > $general->referral_daily_limit) {
                return [
                    'allowed' => false,
                    'reason' => 'Daily referral limit exceeded'
                ];
            }
        }
        
        return ['allowed' => true];
    }

    /**
     * Calculate user activity score
     */
    protected function calculateActivityScore(User $user, int $months): float
    {
        $score = 0;
        $startDate = now()->subMonths($months);
        
        // Transaction activity (40% weight)
        $transactionCount = $user->transactions()
            ->where('created_at', '>=', $startDate)
            ->count();
        $score += min($transactionCount * 2, 40);
        
        // Product upload activity (30% weight)
        $uploadCount = $user->productUploads()
            ->where('created_at', '>=', $startDate)
            ->count();
        $score += min($uploadCount * 5, 30);
        
        // Referral activity (20% weight)
        $referralCount = User::where('ref_by', $user->id)
            ->where('created_at', '>=', $startDate)
            ->count();
        $score += min($referralCount * 10, 20);
        
        // Login consistency (10% weight)
        $loginDays = $user->loginLogs()
            ->where('created_at', '>=', $startDate)
            ->distinct('login_date')
            ->count();
        $score += min($loginDays * 0.5, 10);
        
        return round($score, 2);
    }

    /**
     * Get rebate summary for user
     */
    public function getUserRebateSummary(User $user): array
    {
        $totalEarned = RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('rebate_amount');
            
        $pendingAmount = RebateTransaction::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('rebate_amount');
            
        $thisMonthEarned = UserRebate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('rebate_amount');
            
        $activityScore = $this->calculateActivityScore($user, 1);
        $userTier = $this->tierService->getUserTier($user);
        
        return [
            'total_earned' => $totalEarned,
            'pending_amount' => $pendingAmount,
            'this_month_earned' => $thisMonthEarned,
            'activity_score' => $activityScore,
            'user_tier' => $userTier,
            'tier_multiplier' => $this->tierService->getTierMultiplier($user)
        ];
    }
}