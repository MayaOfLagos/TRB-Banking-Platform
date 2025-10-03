<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\UserRebate;
use App\Models\ProductUpload;
use App\Http\Resources\RebateProgramResource;
use App\Http\Resources\RebateCategoryResource;
use App\Http\Resources\UserRebateResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RebateController extends Controller
{
    //

    /**
     * Get user dashboard data
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            // Get user tier information
            $tierInfo = $this->getUserTierInfo($user->id);
            
            // Get rebate statistics
            $stats = $this->getUserRebateStats($user);
            
            // Get recent rebates
            $recentRebates = UserRebate::with(['program', 'product_upload'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            // Get recommended programs
            $recommendedPrograms = RebateProgram::where('status', 'active')
                ->where('featured', true)
                ->limit(3)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tier_info' => $tierInfo,
                    'stats' => $stats,
                    'recent_rebates' => $recentRebates,
                    'recommended_programs' => $recommendedPrograms
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available rebate programs
     */
    public function programs(Request $request)
    {
        try {
            $query = RebateProgram::with(['category'])
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                });

            // Apply filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('featured')) {
                $query->where('featured', $request->featured);
            }

            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            // Pagination
            $programs = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => [
                    'programs' => RebateProgramResource::collection($programs->items()),
                    'pagination' => [
                        'current_page' => $programs->currentPage(),
                        'last_page' => $programs->lastPage(),
                        'per_page' => $programs->perPage(),
                        'total' => $programs->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load programs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get rebate categories
     */
    public function categories()
    {
        try {
            $categories = RebateCategory::withCount(['rebatePrograms' => function($query) {
                $query->where('status', 'active');
            }])
            ->where('status', 'active')
            ->get();

            return response()->json([
                'success' => true,
                'data' => RebateCategoryResource::collection($categories)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's rebate history
     */
    public function history(Request $request)
    {
        try {
            $query = UserRebate::with(['program', 'product_upload'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('program_id')) {
                $query->where('rebate_program_id', $request->program_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Pagination
            $rebates = $query->paginate($request->get('per_page', 20));

            // Calculate summary
            $summaryQuery = UserRebate::where('user_id', Auth::id());
            
            if ($request->has('status') && $request->status !== 'all') {
                $summaryQuery->where('status', $request->status);
            }
            if ($request->has('program_id')) {
                $summaryQuery->where('rebate_program_id', $request->program_id);
            }
            if ($request->has('date_from')) {
                $summaryQuery->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $summaryQuery->whereDate('created_at', '<=', $request->date_to);
            }

            $summary = [
                'total_rebates' => $summaryQuery->count(),
                'total_amount' => $summaryQuery->sum('rebate_amount'),
                'approved_count' => $summaryQuery->where('status', 'approved')->count(),
                'pending_count' => $summaryQuery->where('status', 'pending')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'rebates' => [
                        'data' => UserRebateResource::collection($rebates->items()),
                        'pagination' => [
                            'current_page' => $rebates->currentPage(),
                            'last_page' => $rebates->lastPage(),
                            'per_page' => $rebates->perPage(),
                            'total' => $rebates->total()
                        ]
                    ],
                    'summary' => $summary
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load rebate history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload product for rebate
     */
    public function uploadProduct(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'rebate_program_id' => 'required|exists:rebate_programs,id',
                'purchase_amount' => 'required|numeric|min:0.01|max:999999.99',
                'product_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
                'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                'purchase_date' => 'required|date|before_or_equal:today',
                'store_location' => 'nullable|string|max:255',
                'product_description' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $program = RebateProgram::findOrFail($request->rebate_program_id);

            // Basic fraud detection
            $fraudCheck = $this->checkBasicFraud($user, $request);
            if ($fraudCheck['is_flagged']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload flagged for review: ' . $fraudCheck['reason'],
                    'flagged' => true
                ], 400);
            }

            // Store images
            $productImagePath = $request->file('product_image')->store('uploads/products/' . date('Y/m'), 'public');
            $receiptImagePath = $request->file('receipt_image')->store('uploads/receipts/' . date('Y/m'), 'public');

            // Create product upload record
            $upload = ProductUpload::create([
                'user_id' => $user->id,
                'rebate_program_id' => $request->rebate_program_id,
                'product_image' => $productImagePath,
                'receipt_image' => $receiptImagePath,
                'purchase_amount' => $request->purchase_amount,
                'purchase_date' => $request->purchase_date,
                'store_location' => $request->store_location,
                'product_description' => $request->product_description,
                'status' => 'pending',
                'upload_ip' => $request->ip(),
                'metadata' => json_encode([
                    'user_agent' => $request->userAgent(),
                    'upload_source' => 'mobile_api',
                    'fraud_score' => $fraudCheck['score']
                ])
            ]);

            // Calculate potential rebate
            $rebateAmount = $this->calculateBasicRebate($program, $request->purchase_amount, $user);

            // Create rebate record
            $rebate = UserRebate::create([
                'user_id' => $user->id,
                'rebate_program_id' => $request->rebate_program_id,
                'product_upload_id' => $upload->id,
                'purchase_amount' => $request->purchase_amount,
                'rebate_amount' => $rebateAmount,
                'tier_multiplier' => $this->getUserTierInfo($user->id)['multiplier'],
                'status' => $fraudCheck['is_flagged'] ? 'review' : 'pending',
                'review_notes' => $fraudCheck['is_flagged'] ? $fraudCheck['reason'] : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product uploaded successfully',
                'data' => [
                    'upload_id' => $upload->id,
                    'rebate_id' => $rebate->id,
                    'estimated_rebate' => $rebateAmount,
                    'status' => $rebate->status,
                    'requires_review' => $fraudCheck['is_flagged']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tier information and progress
     */
    public function tierInfo()
    {
        try {
            $user = Auth::user();
            $tierInfo = $this->getUserTierInfo($user->id);
            $tierProgress = $this->calculateTierProgress($user, $tierInfo);
            $tierBenefits = $this->getTierBenefits();
            $achievements = $this->getUserAchievements($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'tier_info' => $tierInfo,
                    'tier_progress' => $tierProgress,
                    'tier_benefits' => $tierBenefits,
                    'achievements' => $achievements
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tier information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upload status
     */
    public function uploadStatus($uploadId)
    {
        try {
            $upload = ProductUpload::with(['rebate_program', 'user_rebate'])
                ->where('user_id', Auth::id())
                ->findOrFail($uploadId);

            return response()->json([
                'success' => true,
                'data' => [
                    'upload' => $upload,
                    'rebate' => $upload->user_rebate
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get notifications
     */
    public function notifications(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get recent rebate updates
            $recentUpdates = UserRebate::with(['program'])
                ->where('user_id', $user->id)
                ->where('updated_at', '>=', now()->subDays(7))
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get();

            $notifications = [];

            foreach ($recentUpdates as $rebate) {
                $notifications[] = [
                    'id' => 'rebate_' . $rebate->id,
                    'type' => 'rebate_update',
                    'title' => $this->getNotificationTitle($rebate),
                    'message' => $this->getNotificationMessage($rebate),
                    'date' => $rebate->updated_at,
                    'read' => false, // Could implement read tracking
                    'data' => [
                        'rebate_id' => $rebate->id,
                        'status' => $rebate->status,
                        'amount' => $rebate->rebate_amount
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods (same as web controller)
     */
    private function getUserTierInfo($userId)
    {
        $totalEarned = UserRebate::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('rebate_amount');

        $tier = 'Bronze';
        $multiplier = 1.0;

        if ($totalEarned >= 15000) {
            $tier = 'Platinum';
            $multiplier = 2.0;
        } elseif ($totalEarned >= 5000) {
            $tier = 'Gold';
            $multiplier = 1.5;
        } elseif ($totalEarned >= 1000) {
            $tier = 'Silver';
            $multiplier = 1.2;
        }

        return [
            'tier' => $tier,
            'multiplier' => $multiplier,
            'total_earned' => $totalEarned,
        ];
    }

    private function getUserRebateStats($user)
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'total_earned' => UserRebate::where('user_id', $user->id)
                ->where('status', 'approved')
                ->sum('rebate_amount'),
            
            'pending_amount' => UserRebate::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('rebate_amount'),
            
            'this_month' => UserRebate::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where('created_at', '>=', $currentMonth)
                ->sum('rebate_amount'),
            
            'total_rebates' => UserRebate::where('user_id', $user->id)->count(),
            
            'approved_rebates' => UserRebate::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count(),
        ];
    }

    private function calculateTierProgress($user, $tierInfo)
    {
        $totalEarned = UserRebate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('rebate_amount');

        $tierThresholds = [
            'Bronze' => 0,
            'Silver' => 1000,
            'Gold' => 5000,
            'Platinum' => 15000,
        ];

        $currentTier = $tierInfo['tier'] ?? 'Bronze';
        $nextTier = $this->getNextTier($currentTier);
        
        if (!$nextTier) {
            return [
                'current_tier' => $currentTier,
                'progress_percentage' => 100,
                'amount_to_next' => 0,
                'next_tier' => null,
            ];
        }

        $currentThreshold = $tierThresholds[$currentTier];
        $nextThreshold = $tierThresholds[$nextTier];
        
        $progressAmount = $totalEarned - $currentThreshold;
        $tierRange = $nextThreshold - $currentThreshold;
        $progressPercentage = $tierRange > 0 ? min(100, ($progressAmount / $tierRange) * 100) : 0;

        return [
            'current_tier' => $currentTier,
            'next_tier' => $nextTier,
            'progress_percentage' => $progressPercentage,
            'amount_to_next' => max(0, $nextThreshold - $totalEarned),
            'total_earned' => $totalEarned,
            'current_threshold' => $currentThreshold,
            'next_threshold' => $nextThreshold,
        ];
    }

    private function getNextTier($currentTier)
    {
        $tiers = ['Bronze', 'Silver', 'Gold', 'Platinum'];
        $currentIndex = array_search($currentTier, $tiers);
        
        return $currentIndex !== false && $currentIndex < count($tiers) - 1 
            ? $tiers[$currentIndex + 1] 
            : null;
    }

    private function getTierBenefits()
    {
        return [
            'Bronze' => [
                'multiplier' => 1.0,
                'benefits' => [
                    'Standard rebate rates',
                    'Basic customer support',
                    'Monthly program updates'
                ]
            ],
            'Silver' => [
                'multiplier' => 1.2,
                'benefits' => [
                    '20% bonus on all rebates',
                    'Priority customer support',
                    'Exclusive Silver programs',
                    'Weekly program updates'
                ]
            ],
            'Gold' => [
                'multiplier' => 1.5,
                'benefits' => [
                    '50% bonus on all rebates',
                    'Premium customer support',
                    'Exclusive Gold programs',
                    'Early access to new programs',
                    'Daily program updates'
                ]
            ],
            'Platinum' => [
                'multiplier' => 2.0,
                'benefits' => [
                    '100% bonus on all rebates',
                    'VIP customer support',
                    'Exclusive Platinum programs',
                    'Beta access to new features',
                    'Personal account manager',
                    'Real-time notifications'
                ]
            ],
        ];
    }

    private function getUserAchievements($user)
    {
        $totalEarned = UserRebate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('rebate_amount');

        $totalRebates = UserRebate::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $achievements = [];

        // Earning milestones
        if ($totalEarned >= 100) $achievements[] = ['title' => 'First $100 Earned', 'icon' => 'trophy', 'date' => 'Achieved'];
        if ($totalEarned >= 500) $achievements[] = ['title' => 'Big Earner - $500', 'icon' => 'star', 'date' => 'Achieved'];
        if ($totalEarned >= 1000) $achievements[] = ['title' => 'Silver Milestone', 'icon' => 'medal', 'date' => 'Achieved'];
        if ($totalEarned >= 5000) $achievements[] = ['title' => 'Gold Milestone', 'icon' => 'crown', 'date' => 'Achieved'];

        // Activity milestones
        if ($totalRebates >= 5) $achievements[] = ['title' => 'Getting Started', 'icon' => 'rocket', 'date' => 'Achieved'];
        if ($totalRebates >= 25) $achievements[] = ['title' => 'Active Participant', 'icon' => 'fire', 'date' => 'Achieved'];
        if ($totalRebates >= 100) $achievements[] = ['title' => 'Rebate Expert', 'icon' => 'graduation-cap', 'date' => 'Achieved'];

        return $achievements;
    }

    private function getNotificationTitle($rebate)
    {
        switch ($rebate->status) {
            case 'approved':
                return 'Rebate Approved!';
            case 'rejected':
                return 'Rebate Update';
            case 'pending':
                return 'Rebate Submitted';
            default:
                return 'Rebate Status Update';
        }
    }

    private function getNotificationMessage($rebate)
    {
        switch ($rebate->status) {
            case 'approved':
                return "Your rebate of $" . number_format($rebate->rebate_amount, 2) . " has been approved and credited to your account.";
            case 'rejected':
                return "Your rebate submission has been reviewed. Please check the details for more information.";
            case 'pending':
                return "Your rebate submission of $" . number_format($rebate->rebate_amount, 2) . " is being reviewed.";
            default:
                return "There has been an update to your rebate submission.";
        }
    }

    /**
     * Basic fraud detection
     */
    private function checkBasicFraud($user, $request)
    {
        $score = 0;
        $reasons = [];

        // Check upload frequency (max 10 per day)
        $todayUploads = ProductUpload::where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->count();

        if ($todayUploads >= 10) {
            $score += 50;
            $reasons[] = 'Excessive uploads per day';
        }

        // Check duplicate purchases
        $duplicates = ProductUpload::where('user_id', $user->id)
            ->where('purchase_amount', $request->purchase_amount)
            ->whereDate('purchase_date', $request->purchase_date)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($duplicates > 0) {
            $score += 30;
            $reasons[] = 'Potential duplicate purchase';
        }

        // Check purchase amount (flag extremely high amounts)
        if ($request->purchase_amount > 1000) {
            $score += 20;
            $reasons[] = 'High purchase amount requires verification';
        }

        return [
            'score' => $score,
            'is_flagged' => $score >= 50,
            'reason' => implode('; ', $reasons)
        ];
    }

    /**
     * Basic rebate calculation
     */
    private function calculateBasicRebate($program, $purchaseAmount, $user)
    {
        // Get user tier multiplier
        $tierInfo = $this->getUserTierInfo($user->id);
        $tierMultiplier = $tierInfo['multiplier'];

        // Calculate base rebate
        $baseRebate = ($purchaseAmount * $program->rebate_rate / 100);

        // Apply tier multiplier
        $rebateAmount = $baseRebate * $tierMultiplier;

        // Apply maximum rebate limit
        if ($program->max_rebate_amount && $rebateAmount > $program->max_rebate_amount) {
            $rebateAmount = $program->max_rebate_amount;
        }

        return round($rebateAmount, 2);
    }
}