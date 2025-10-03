<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProductUpload;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class FraudDetectionService
{
    protected $suspiciousThresholds = [
        'daily_uploads' => 50,        // Default: allow more daily uploads
        'rapid_uploads' => 15,        // Default: allow burst uploads
        'duplicate_image_threshold' => 0.85,
        'velocity_threshold' => 25,   // Default: allow more per hour
        'ip_sharing_threshold' => 15  // Default: allow more users per IP
    ];

    /**
     * Get fraud settings from general settings
     */
    protected function getFraudSettings(): array
    {
        return getRebateFraudSettings();
    }

    /**
     * Validate product upload for fraud
     */
    public function validateProductUpload(ProductUpload $productUpload): array
    {
        // Check if fraud detection is enabled
        $fraudSettings = $this->getFraudSettings();
        
        if (!($fraudSettings['enabled'] ?? true)) {
            // Fraud detection is disabled - allow all uploads
            return [
                'valid' => true,
                'requires_review' => false,
                'score' => 0,
                'flags' => [],
                'reason' => 'Fraud detection disabled',
                'risk_level' => 'low'
            ];
        }

        $fraudScore = 0;
        $flags = [];
        
        try {
            // Check KYC verification status first
            $kycCheck = $this->checkKycVerification($productUpload->user);
            $fraudScore += $kycCheck['score'];
            if ($kycCheck['flagged']) {
                $flags[] = 'kyc_unverified';
            }

            // Check upload velocity
            $velocityCheck = $this->checkUploadVelocity($productUpload);
            $fraudScore += $velocityCheck['score'];
            if ($velocityCheck['flagged']) {
                $flags[] = 'high_velocity';
            }

            // Check duplicate content
            $duplicateCheck = $this->checkDuplicateContent($productUpload);
            $fraudScore += $duplicateCheck['score'];
            if ($duplicateCheck['flagged']) {
                $flags[] = 'duplicate_content';
            }

            // Check IP abuse
            $ipCheck = $this->checkIPAbuse($productUpload);
            $fraudScore += $ipCheck['score'];
            if ($ipCheck['flagged']) {
                $flags[] = 'ip_abuse';
            }

            // Check user behavior patterns
            $behaviorCheck = $this->checkUserBehavior($productUpload->user);
            $fraudScore += $behaviorCheck['score'];
            if ($behaviorCheck['flagged']) {
                $flags[] = 'suspicious_behavior';
            }

            // Check geographic anomalies
            $geoCheck = $this->checkGeographicAnomalies($productUpload);
            $fraudScore += $geoCheck['score'];
            if ($geoCheck['flagged']) {
                $flags[] = 'geographic_anomaly';
            }

            // Use fraud score threshold from settings
            $fraudThreshold = $fraudSettings['fraud_score_threshold'] ?? 70;
            $reviewThreshold = max(50, $fraudThreshold - 20); // Review threshold is 20 points below fraud threshold
            
            // KYC-verified users get enhanced instant approval consideration
            $isKycVerified = $productUpload->user->kv == \App\Constants\Status::KYC_VERIFIED;
            $instantApprovalEnabled = $this->isInstantApprovalEnabled($productUpload);
            
            // If user is KYC verified and instant approval is enabled, reduce thresholds significantly  
            if ($isKycVerified && $instantApprovalEnabled) {
                $fraudThreshold = min($fraudThreshold + 30, 90); // Increase threshold by 30 points (more lenient)
                $reviewThreshold = max($reviewThreshold - 15, 20); // Reduce review threshold by 15 points
                $flags[] = 'kyc_instant_approval_eligible';
            }
            
            $isValid = $fraudScore < $fraudThreshold;
            $requiresReview = $fraudScore >= $reviewThreshold && $fraudScore < $fraudThreshold;

            // Log suspicious activity if above review threshold
            if ($fraudScore >= $reviewThreshold) {
                $this->logSuspiciousActivity($productUpload, $fraudScore, $flags);
            }

            return [
                'valid' => $isValid,
                'requires_review' => $requiresReview,
                'score' => $fraudScore,
                'flags' => $flags,
                'reason' => $this->getFraudReason($flags),
                'risk_level' => $this->getRiskLevel($fraudScore),
                'kyc_verified' => $isKycVerified,
                'instant_approval_eligible' => $isKycVerified && $instantApprovalEnabled
            ];

        } catch (\Exception $e) {
            Log::error('Fraud detection error: ' . $e->getMessage(), [
                'product_upload_id' => $productUpload->id,
                'user_id' => $productUpload->user_id,
                'trace' => $e->getTraceAsString()
            ]);

            // For now, let's be lenient and allow uploads when system error occurs
            return [
                'valid' => true,  // Changed to true - don't reject due to system error
                'score' => 0,     // Changed to 0 - no fraud score on system error
                'flags' => ['system_error'],
                'reason' => 'Fraud detection system error - defaulting to allow',
                'risk_level' => 'low',  // Changed to low
                'requires_review' => false  // Changed to false - don't require review for system errors
            ];
        }
    }

    /**
     * Check upload velocity patterns
     */
    protected function checkUploadVelocity(ProductUpload $productUpload): array
    {
        $user = $productUpload->user;
        $score = 0;
        $flagged = false;
        
        // Get fraud settings
        $fraudSettings = $this->getFraudSettings();

        // Check uploads in last 24 hours
        $dailyUploads = ProductUpload::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $maxDailyUploads = $fraudSettings['max_daily_uploads'] ?? 50;
        if ($dailyUploads > $maxDailyUploads) {
            $score += 20;
            $flagged = true;
        }

        // Check uploads in last 10 minutes
        $rapidUploads = ProductUpload::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        $maxRapidUploads = $fraudSettings['max_rapid_uploads'] ?? 15;
        if ($rapidUploads > $maxRapidUploads) {
            $score += 15;
            $flagged = true;
        }

        // Check hourly velocity
        $hourlyUploads = ProductUpload::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        $velocityThreshold = $fraudSettings['velocity_threshold'] ?? 25;
        if ($hourlyUploads > $velocityThreshold) {
            $score += 10;
            $flagged = true;
        }

        return [
            'score' => $score,
            'flagged' => $flagged,
            'daily_count' => $dailyUploads,
            'rapid_count' => $rapidUploads,
            'hourly_count' => $hourlyUploads
        ];
    }

    /**
     * Check for duplicate content
     */
    protected function checkDuplicateContent(ProductUpload $productUpload): array
    {
        $score = 0;
        $flagged = false;
        
        // Get fraud settings
        $fraudSettings = $this->getFraudSettings();
        
        // Check if duplicate detection is enabled
        if (!($fraudSettings['duplicate_detection'] ?? true)) {
            return [
                'score' => 0,
                'flagged' => false,
                'reason' => 'Duplicate detection disabled'
            ];
        }

        // Check for exact duplicate images
        if ($productUpload->receipt_image_hash) {
            $duplicates = ProductUpload::where('receipt_image_hash', $productUpload->receipt_image_hash)
                ->where('id', '!=', $productUpload->id)
                ->count();

            if ($duplicates > 0) {
                $score += 50;
                $flagged = true;
            }
        }

        // Check for similar product names/descriptions
        $similarProducts = ProductUpload::where('user_id', '!=', $productUpload->user_id)
            ->where(function($query) use ($productUpload) {
                $query->where('product_name', 'LIKE', '%' . $productUpload->product_name . '%')
                      ->orWhere('description', 'LIKE', '%' . $productUpload->description . '%');
            })
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count();

        if ($similarProducts > 3) {
            $score += 15;
        }

        return [
            'score' => $score,
            'flagged' => $flagged,
            'exact_duplicates' => $duplicates ?? 0,
            'similar_products' => $similarProducts
        ];
    }

    /**
     * Check IP address abuse
     */
    protected function checkIPAbuse(ProductUpload $productUpload): array
    {
        $score = 0;
        $flagged = false;
        $userIP = $productUpload->ip_address ?? request()->ip();
        
        // Get fraud settings
        $fraudSettings = $this->getFraudSettings();

        // Check how many different users are using this IP
        $ipUsers = ProductUpload::where('ip_address', $userIP)
            ->distinct('user_id')
            ->count('user_id');

        $ipSharingLimit = $fraudSettings['ip_sharing_limit'] ?? 15;
        if ($ipUsers > $ipSharingLimit) {
            $score += 25;
            $flagged = true;
        }

        // Check for IP geolocation changes - more lenient
        $recentIPs = ProductUpload::where('user_id', $productUpload->user_id)
            ->where('created_at', '>=', now()->subDays(7))
            ->distinct('ip_address')
            ->pluck('ip_address');

        if ($recentIPs->count() > 10) { // Increased from 5 to 10
            $score += 10; // Reduced from 20
        }

        // Check for blacklisted IPs (you would maintain this list)
        if ($this->isBlacklistedIP($userIP)) {
            $score += 60;
            $flagged = true;
        }

        return [
            'score' => $score,
            'flagged' => $flagged,
            'ip_users' => $ipUsers,
            'recent_ips' => $recentIPs->count()
        ];
    }

    /**
     * Check user behavior patterns
     */
    protected function checkUserBehavior(User $user): array
    {
        $score = 0;
        $flagged = false;

        // Check account age vs activity - more lenient
        $accountAge = $user->created_at->diffInDays(now());
        $totalUploads = $user->productUploads()->count();

        // Only flag extremely new accounts with excessive activity
        if ($accountAge < 3 && $totalUploads > 50) {
            $score += 15; // Reduced from 25
            $flagged = true;
        }

        // Remove rebate success rate check - high success rate is good!
        // Don't penalize users for having legitimate receipts

        // Check for unusual patterns in upload times - more lenient
        $nightUploads = ProductUpload::where('user_id', $user->id)
            ->whereTime('created_at', '>=', '00:00:00')
            ->whereTime('created_at', '<=', '05:00:00')
            ->count();
            
        $totalUploads = ProductUpload::where('user_id', $user->id)->count();
        
        // Only flag if ALL uploads are at night (very suspicious)
        if ($totalUploads > 10 && ($nightUploads / $totalUploads) > 0.8) {
            $score += 5; // Reduced from 10
        }

        // Don't penalize incomplete profiles - many legitimate users don't complete profiles
        // Removed profile completeness check

        return [
            'score' => $score,
            'flagged' => $flagged,
            'account_age' => $accountAge,
            'upload_ratio' => $totalUploads > 0 ? ($totalUploads / max($accountAge, 1)) : 0
        ];
    }

    /**
     * Check for geographic anomalies
     */
    protected function checkGeographicAnomalies(ProductUpload $productUpload): array
    {
        $score = 0;
        $flagged = false;

        // This would integrate with IP geolocation service
        // For now, basic implementation
        $userIP = $productUpload->ip_address ?? request()->ip();
        
        // Check if user suddenly changed locations
        $recentUploads = ProductUpload::where('user_id', $productUpload->user_id)
            ->where('created_at', '>=', now()->subDays(3))
            ->whereNot('id', $productUpload->id)
            ->pluck('ip_address')
            ->unique();

        // Only flag if excessive IP changes (could indicate VPN hopping)
        if ($recentUploads->count() > 8) { // Increased from 3 to 8
            $score += 10; // Reduced from 20
        }

        return [
            'score' => $score,
            'flagged' => $flagged,
            'recent_ip_count' => $recentUploads->count()
        ];
    }

    /**
     * Check KYC verification status
     */
    protected function checkKycVerification(User $user): array
    {
        $score = 0;
        $flagged = false;

        // KYC verification status check
        if ($user->kv == \App\Constants\Status::KYC_UNVERIFIED) {
            $score += 40; // High score for unverified users
            $flagged = true;
        } elseif ($user->kv == \App\Constants\Status::KYC_PENDING) {
            $score += 20; // Medium score for pending verification
            $flagged = true;
        } elseif ($user->kv == \App\Constants\Status::KYC_VERIFIED) {
            $score -= 15; // Bonus for verified users (negative score reduces fraud score)
        }

        return [
            'score' => $score,
            'flagged' => $flagged,
            'kyc_status' => $user->kv,
            'is_verified' => $user->kv == \App\Constants\Status::KYC_VERIFIED
        ];
    }

    /**
     * Check if instant approval is enabled for this upload
     */
    protected function isInstantApprovalEnabled(ProductUpload $productUpload): bool
    {
        // Check if the program has instant approval enabled
        $program = $productUpload->rebateProgram;
        if (!$program) {
            return false;
        }

        $programSettings = json_decode($program->settings ?? '{}', true);
        $programAutoApproval = $programSettings['auto_approval'] ?? false;

        // Also check global system settings
        $systemSettings = getRebateSettings();
        $systemAutoApproval = $systemSettings['system']['auto_approval'] ?? false;

        // Both program and system must have auto approval enabled
        return $programAutoApproval && $systemAutoApproval;
    }

    /**
     * Check if IP is blacklisted
     */
    protected function isBlacklistedIP(string $ip): bool
    {
        // Implement your blacklist logic here
        // Could be database table, cache, or external service
        $blacklistedIPs = [
            // Add known problematic IPs
        ];
        
        return in_array($ip, $blacklistedIPs);
    }

    /**
     * Log suspicious activity
     */
    protected function logSuspiciousActivity(ProductUpload $productUpload, int $score, array $flags): void
    {
        try {
            DB::table('fraud_logs')->insert([
                'user_id' => $productUpload->user_id,
                'product_upload_id' => $productUpload->id,
                'fraud_score' => $score,
                'flags' => json_encode($flags),
                'ip_address' => $productUpload->ip_address ?? request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // If fraud_logs table doesn't exist, just log to Laravel logs
            Log::info('Suspicious activity detected', [
                'user_id' => $productUpload->user_id,
                'product_upload_id' => $productUpload->id,
                'fraud_score' => $score,
                'flags' => $flags
            ]);
        }

        Log::warning('Suspicious activity detected', [
            'user_id' => $productUpload->user_id,
            'product_upload_id' => $productUpload->id,
            'fraud_score' => $score,
            'flags' => $flags
        ]);
    }

    /**
     * Get fraud reason description
     */
    protected function getFraudReason(array $flags): string
    {
        $reasons = [
            'high_velocity' => 'Unusually high upload frequency',
            'duplicate_content' => 'Duplicate or similar content detected',
            'ip_abuse' => 'IP address showing suspicious patterns',
            'suspicious_behavior' => 'User behavior patterns indicate potential fraud',
            'geographic_anomaly' => 'Unusual geographic activity patterns',
            'system_error' => 'System error during fraud detection',
            'kyc_unverified' => 'User KYC verification status requires review',
            'kyc_instant_approval_eligible' => 'KYC verified user eligible for instant approval'
        ];

        $activeReasons = array_intersect_key($reasons, array_flip($flags));
        
        return implode(', ', $activeReasons);
    }

    /**
     * Get risk level based on score
     */
    protected function getRiskLevel(int $score): string
    {
        if ($score >= 70) return 'high';
        if ($score >= 40) return 'medium';
        if ($score >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Validate user for rebate eligibility
     */
    public function validateUserForRebate(User $user): array
    {
        $score = 0;
        $flags = [];

        // Check recent rejections
        $recentRejections = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentRejections > 5) {
            $score += 40;
            $flags[] = 'high_rejection_rate';
        }

        // Check account flags
        if ($user->status != 1) {
            $score += 100;
            $flags[] = 'account_suspended';
        }

        return [
            'eligible' => $score < 50,
            'score' => $score,
            'flags' => $flags,
            'reason' => $this->getFraudReason($flags)
        ];
    }

    /**
     * Generate fraud report for admin
     */
    public function generateFraudReport(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_uploads' => ProductUpload::where('created_at', '>=', $startDate)->count(),
            'flagged_uploads' => DB::table('fraud_logs')
                ->where('created_at', '>=', $startDate)
                ->where('fraud_score', '>=', 40)
                ->count(),
            'high_risk_users' => DB::table('fraud_logs')
                ->where('created_at', '>=', $startDate)
                ->where('fraud_score', '>=', 70)
                ->distinct('user_id')
                ->count('user_id'),
            'common_flags' => DB::table('fraud_logs')
                ->where('created_at', '>=', $startDate)
                ->selectRaw('flags, COUNT(*) as count')
                ->groupBy('flags')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            'ip_analysis' => DB::table('fraud_logs')
                ->where('created_at', '>=', $startDate)
                ->selectRaw('ip_address, COUNT(DISTINCT user_id) as user_count')
                ->groupBy('ip_address')
                ->having('user_count', '>', 3)
                ->orderBy('user_count', 'desc')
                ->get()
        ];
    }
}