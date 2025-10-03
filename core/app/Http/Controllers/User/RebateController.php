<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserRebate;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\RebateTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RebateController extends Controller
{
    /**
     * Check if rebate system is enabled, redirect if not
     */
    private function ensureRebateSystemEnabled()
    {
        if (!isRebateSystemEnabled()) {
            $notify[] = ['error', 'Rebate system is currently disabled. Please contact administrator.'];
            return redirect()->route('user.home')->withNotify($notify);
        }
        return null;
    }

    /**
     * User rebate dashboard
     */
    public function dashboard()
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $user = Auth::user();
        $pageTitle = "Rebate Dashboard";
        $general = gs(); // Get general settings

        // Get user's rebate statistics
        $stats = $this->getUserRebateStats($user);
        
        // Get user's tier information
        $tierInfo = $this->getUserTierInfo($user->id);
        
        // Get recent rebate transactions
        $recentRebates = \App\Models\RebateTransaction::with(['rebateCategory.program'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Get available programs for user
        $availablePrograms = RebateProgram::with('rebateCategory')
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->limit(6)
            ->get();

        // Get pending rebates count
        $pendingRebatesCount = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Calculate tier progress
        $tierProgress = $this->calculateTierProgress($user, $tierInfo);

        return view(activeTemplate() . 'user.rebate.dashboard', compact(
            'pageTitle',
            'general',
            'stats',
            'tierInfo',
            'recentRebates',
            'availablePrograms',
            'pendingRebatesCount',
            'tierProgress'
        ));
    }

    /**
     * User rebate history
     */
    public function history(Request $request)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $pageTitle = "Rebate History";
        $user = Auth::user();
        $general = gs(); // Get general settings

        $rebates = \App\Models\RebateTransaction::with(['rebateCategory.program', 'product_upload'])
            ->where('user_id', $user->id)
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->program_id, function($query, $programId) {
                $query->where('rebate_category_id', function($subQuery) use ($programId) {
                    $subQuery->select('id')
                        ->from('rebate_categories')
                        ->where('rebate_program_id', $programId);
                });
            })
            ->when($request->date_range, function($query, $dateRange) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) == 2) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($dates[0])->startOfDay(),
                        Carbon::parse($dates[1])->endOfDay()
                    ]);
                }
            })
            ->latest()
            ->paginate(getPaginate());

        // Get filter options
        $programs = RebateProgram::where('is_active', true)->get();
        
        // Calculate summary stats for filtered results
        $summaryStats = [
            'total_rebates' => $rebates->total(),
            'total_amount' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->when($request->status, fn($q, $s) => $q->where('status', $s))
                ->when($request->program_id, fn($q, $p) => $q->where('rebate_program_id', $p))
                ->sum('final_amount'),
            'approved_count' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->where('status', 'processed')
                ->when($request->program_id, fn($q, $p) => $q->where('rebate_program_id', $p))
                ->count(),
        ];

        return view(activeTemplate() . 'user.rebate.history', compact(
            'pageTitle',
            'general',
            'rebates',
            'programs',
            'summaryStats'
        ));
    }

    /**
     * Show specific rebate transaction details
     */
    public function show($id)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $user = Auth::user();
        $general = gs(); // Get general settings
        $rebate = \App\Models\RebateTransaction::with(['rebateCategory', 'rebateProgram', 'product_upload'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $pageTitle = "Rebate Details";

        return view(activeTemplate() . 'user.rebate.show', compact(
            'pageTitle',
            'general',
            'rebate'
        ));
    }

    /**
     * Show available programs
     */
    public function programs()
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $pageTitle = "Available Programs";
        $user = Auth::user();
        $general = gs(); // Get general settings
        $tierInfo = $this->getUserTierInfo($user->id);

        // Get active programs with their categories
        $programs = RebateProgram::with(['categories' => function($query) {
            $query->where('is_active', true);
        }, 'rebateTransactions'])
        ->where('is_active', true)
        ->where(function($query) {
            $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
        })
        ->where(function($query) {
            $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
        })
        ->get();
        
        // Filter out programs that have no active categories (after eager loading)
        $programs = $programs->filter(function($program) {
            return $program->categories->count() > 0;
        });

        // Calculate stats for the programs page
        $totalPrograms = $programs->count();
        
        // Count programs where user has participated (has rebate transactions)
        $joinedPrograms = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->whereNotNull('rebate_program_id')
            ->distinct('rebate_program_id')
            ->count('rebate_program_id');
        
        // Calculate total earnings from all approved rebate transactions
        $totalProgramEarnings = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('rebate_amount');
        
        $avgRebateRate = $programs->avg('default_rate') ?? 0;
        
        $stats = [
            'total_programs' => $totalPrograms,
            'joined_programs' => $joinedPrograms,
            'avg_rebate_rate' => number_format($avgRebateRate, 1),
            'active_categories' => $programs->pluck('categories')->flatten()->where('is_active', true)->count(),
            'total_program_earnings' => $totalProgramEarnings
        ];

        return view(activeTemplate() . 'user.rebate.programs', compact(
            'pageTitle',
            'general',
            'programs',
            'tierInfo',
            'stats'
        ));
    }

    /**
     * User tier information
     */
    public function tiers()
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $pageTitle = "Tier Benefits";
        $user = Auth::user();
        $general = gs(); // Get general settings
        
        $tierInfo = $this->getUserTierInfo($user->id);
        $tierProgress = $this->calculateTierProgress($user, $tierInfo);
        
        // Get tier benefits breakdown
        $tierBenefits = $this->getTierBenefits();
        
        // Get user's achievement history
        $achievements = $this->getUserAchievements($user);

        return view(activeTemplate() . 'user.rebate.tiers', compact(
            'pageTitle',
            'general',
            'tierInfo',
            'tierProgress',
            'tierBenefits',
            'achievements'
        ));
    }

    /**
     * Get user rebate statistics
     */
    private function getUserRebateStats($user)
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Get or create user rebate summary record
        $userRebate = UserRebate::firstOrCreate(
            ['user_id' => $user->id],
            [
                'total_earned' => 0,
                'current_balance' => 0,
                'total_redeemed' => 0,
                'pending_amount' => 0,
                'current_tier' => 1
            ]
        );

        return [
            'total_earned' => $userRebate->total_earned,
            
            'pending_amount' => $userRebate->pending_amount,
            
            'this_month' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->where('status', 'processed')
                ->where('created_at', '>=', $currentMonth)
                ->sum('final_amount'),
            
            'last_month' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->where('status', 'processed')
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->sum('final_amount'),
            
            'total_rebates' => \App\Models\RebateTransaction::where('user_id', $user->id)->count(),
            
            'approved_rebates' => \App\Models\RebateTransaction::where('user_id', $user->id)
                ->where('status', 'processed')
                ->count(),
        ];
    }

    /**
     * Calculate tier progress
     */
    private function calculateTierProgress($user, $tierInfo)
    {
        $totalEarned = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'processed')
            ->sum('final_amount');

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

    /**
     * Get next tier
     */
    private function getNextTier($currentTier)
    {
        $tiers = ['Bronze', 'Silver', 'Gold', 'Platinum'];
        $currentIndex = array_search($currentTier, $tiers);
        
        return $currentIndex !== false && $currentIndex < count($tiers) - 1 
            ? $tiers[$currentIndex + 1] 
            : null;
    }

    /**
     * Get tier benefits
     */
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

    /**
     * Get user achievements
     */
    private function getUserAchievements($user)
    {
        $totalEarned = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'processed')
            ->sum('final_amount');

        $totalRebates = \App\Models\RebateTransaction::where('user_id', $user->id)
            ->where('status', 'processed')
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

    /**
     * Get user tier information
     */
    private function getUserTierInfo($userId)
    {
        $totalEarned = \App\Models\RebateTransaction::where('user_id', $userId)
            ->where('status', 'processed')
            ->sum('final_amount');

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

    /**
     * Export user's rebate data
     */
    public function export(Request $request)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'export_date_range' => 'nullable|string'
        ]);

        try {
            $format = $request->get('format');
            $dateRange = $request->get('export_date_range');
            
            $query = \App\Models\RebateTransaction::with(['rebateCategory.program', 'product_upload'])
                ->where('user_id', $user->id);

            // Apply date range filter if provided
            if ($dateRange) {
                $dates = explode(' - ', $dateRange);
                if (count($dates) == 2) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($dates[0])->startOfDay(),
                        Carbon::parse($dates[1])->endOfDay()
                    ]);
                }
            }

            // Apply current filters from request
            $query->when($request->status, function($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->program_id, function($q, $programId) {
                $q->where('rebate_category_id', function($subQuery) use ($programId) {
                    $subQuery->select('id')
                        ->from('rebate_categories')
                        ->where('rebate_program_id', $programId);
                });
            });

            $rebates = $query->latest()->get();

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($rebates, $user);
                case 'excel':
                    $notify[] = ['info', 'Excel export coming soon'];
                    return back()->withNotify($notify);
                case 'pdf':
                    $notify[] = ['info', 'PDF export coming soon'];
                    return back()->withNotify($notify);
                default:
                    $notify[] = ['error', 'Invalid export format'];
                    return back()->withNotify($notify);
            }

        } catch (\Exception $e) {
            Log::error('User rebate export error: ' . $e->getMessage());
            $notify[] = ['error', 'Export failed. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Export rebates to CSV
     */
    protected function exportToCsv($rebates, $user)
    {
        $filename = "rebate_history_{$user->username}_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($rebates) {
            $output = fopen('php://output', 'w');
            
            // Headers
            fputcsv($output, [
                'ID', 'Program', 'Category', 'Type', 'Amount', 'Status', 
                'Submitted Date', 'Processed Date', 'Receipt Number', 'Product Name'
            ]);

            // Data
            foreach ($rebates as $rebate) {
                fputcsv($output, [
                    $rebate->id,
                    $rebate->rebateCategory?->program?->name ?? 'N/A',
                    $rebate->rebateCategory?->name ?? 'N/A',
                    ucwords(str_replace('_', ' ', $rebate->type ?? 'N/A')),
                    '$' . number_format($rebate->rebate_amount, 2),
                    ucfirst($rebate->status),
                    $rebate->created_at->format('Y-m-d H:i:s'),
                    $rebate->approved_at ? $rebate->approved_at->format('Y-m-d H:i:s') : 
                        ($rebate->rejected_at ? $rebate->rejected_at->format('Y-m-d H:i:s') : 'Pending'),
                    $rebate->product_upload?->receipt_number ?? 'N/A',
                    $rebate->product_upload?->product_name ?? 'N/A'
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Get transaction details for a specific rebate
     */
    public function transactions($id)
    {
        // Check if rebate system is enabled
        if ($redirect = $this->ensureRebateSystemEnabled()) {
            return $redirect;
        }
        
        $user = Auth::user();
        
        $rebate = \App\Models\RebateTransaction::with(['rebateCategory.program', 'product_upload'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        $html = view('templates.MayaOfLagos.user.rebate.partials.transaction_details', compact('rebate'))->render();
        
        return response($html);
    }
}