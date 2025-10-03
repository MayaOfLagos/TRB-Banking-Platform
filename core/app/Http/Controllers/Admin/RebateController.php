<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Models\RebateProgram;
use App\Models\RebateCategory;
use App\Models\User;
use App\Services\RebateProcessingService;
use App\Services\FraudDetectionService;
use App\Services\RebateCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RebateController extends Controller
{
    protected $processingService;
    protected $fraudDetectionService;

    public function __construct(
        RebateProcessingService $processingService,
        FraudDetectionService $fraudDetectionService
    ) {
        $this->processingService = $processingService;
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Display rebate dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = 'Rebate Management';

        // Get filters
        $status = $request->get('status', 'all');
        $type = $request->get('type', 'all');
        $dateRange = $request->get('date_range', '30');

        // Build query
        $query = RebateTransaction::with(['user', 'rebateProgram', 'rebateCategory'])
            ->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('transaction_type', $type);
        }

        if ($dateRange !== 'all') {
            $query->where('created_at', '>=', now()->subDays($dateRange));
        }

        $rebates = $query->paginate(getPaginate());

        // Get statistics
        $stats = $this->processingService->getProcessingStats(30);

        // Get fraud report
        $fraudReport = $this->fraudDetectionService->generateFraudReport(30);

        // Get general settings for currency display
        $general = gs();
        
        // Set empty message for no results
        $emptyMessage = 'No rebate transactions found';

        return view('admin.rebate.index', compact('pageTitle', 'rebates', 'stats', 'fraudReport', 'general', 'emptyMessage'));
    }

    /**
     * Show rebate details
     */
    public function show($id)
    {
        $pageTitle = 'Rebate Transaction Details';
        $rebate = RebateTransaction::with([
            'user',
            'rebateProgram',
            'rebateCategory',
            'product_upload'
        ])->findOrFail($id);

        // Get fraud analysis if available
        $fraudAnalysis = null;
        if ($rebate->reference_type === 'App\Models\ProductUpload' && $rebate->reference_id) {
            $productUpload = \App\Models\ProductUpload::find($rebate->reference_id);
            if ($productUpload) {
                $fraudAnalysis = $this->fraudDetectionService->validateProductUpload($productUpload);
            }
        }

        // Get general settings for currency display
        $general = gs();

        return view('admin.rebate.show', compact('pageTitle', 'rebate', 'fraudAnalysis', 'general'));
    }

    /**
     * Approve a rebate
     */
    public function approve(Request $request, $id)
    {
        $rebate = RebateTransaction::findOrFail($id);

        try {
            $result = $this->processingService->approveRebateTransaction($rebate, auth()->guard('admin')->id());

            if ($result['success']) {
                $notify[] = ['success', 'Rebate transaction approved successfully'];
                
                if ($result['tier_upgraded']) {
                    $notify[] = ['info', 'User has been upgraded to ' . ucfirst($result['new_tier']) . ' tier'];
                }
            } else {
                $notify[] = ['error', $result['message']];
            }

        } catch (\Exception $e) {
            Log::error('Rebate approval error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while approving the rebate'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Reject a rebate
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $rebate = RebateTransaction::findOrFail($id);

        try {
            $result = $this->processingService->rejectRebateTransaction(
                $rebate, 
                $request->rejection_reason, 
                auth()->guard('admin')->id()
            );

            if ($result['success']) {
                $notify[] = ['success', 'Rebate rejected successfully'];
            } else {
                $notify[] = ['error', $result['message']];
            }

        } catch (\Exception $e) {
            Log::error('Rebate rejection error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while rejecting the rebate'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Bulk approve rebates
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'rebate_ids' => 'required|array',
            'rebate_ids.*' => 'exists:rebate_transactions,id'
        ]);

        // Additional validation to ensure only pending rebates are approved
        $pendingRebates = RebateTransaction::whereIn('id', $request->rebate_ids)
            ->where('status', 'pending')
            ->pluck('id')
            ->toArray();

        Log::info('Bulk approval request', [
            'requested_ids' => $request->rebate_ids,
            'pending_ids' => $pendingRebates,
            'admin_id' => auth()->guard('admin')->id()
        ]);

        if (empty($pendingRebates)) {
            $notify[] = ['error', 'No valid pending rebates found for approval'];
            return back()->withNotify($notify);
        }

        try {
            $result = $this->processingService->bulkApproveRebates(
                $pendingRebates, 
                auth()->guard('admin')->id()
            );

            Log::info('Bulk approval result', $result);

            if ($result['approved'] > 0) {
                $notify[] = ['success', "Approved {$result['approved']} rebates successfully"];
            } else {
                $notify[] = ['warning', 'No rebates were approved. Check logs for details.'];
            }
            
            if ($result['failed'] > 0) {
                $notify[] = ['warning', "{$result['failed']} rebates failed to approve"];
            }

        } catch (\Exception $e) {
            Log::error('Bulk approval error: ' . $e->getMessage(), [
                'rebate_ids' => $request->rebate_ids,
                'pending_rebates' => $pendingRebates ?? [],
                'admin_id' => auth()->guard('admin')->id()
            ]);
            $notify[] = ['error', 'An error occurred during bulk approval: ' . $e->getMessage()];
        }

        return back()->withNotify($notify);
    }

    /**
     * Show rebate analytics
     */
    public function analytics(Request $request)
    {
        $pageTitle = 'Rebate Analytics';

        $dateRange = $request->get('range', 30);
        $stats = $this->processingService->getProcessingStats($dateRange);
        
        // Calculate additional analytics data
        $previousPeriodStart = now()->subDays($dateRange * 2);
        $currentPeriodStart = now()->subDays($dateRange);
        
        // Get previous period data for growth calculations
        $previousPeriodPaid = RebateTransaction::where('created_at', '>=', $previousPeriodStart)
            ->where('created_at', '<', $currentPeriodStart)
            ->where('status', 'approved')
            ->sum('rebate_amount');
        
        $totalPaidGrowth = $previousPeriodPaid > 0 
            ? (($stats['total_amount_paid'] - $previousPeriodPaid) / $previousPeriodPaid) * 100 
            : 0;
        
        // Get user statistics
        $activeUsers = User::whereHas('rebateTransactions', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Calculate average rebate amount
        $avgRebateAmount = $stats['approved'] > 0 ? $stats['total_amount_paid'] / $stats['approved'] : 0;
        
        // Calculate approval rate
        $approvalRate = $stats['total_processed'] > 0 
            ? ($stats['approved'] / $stats['total_processed']) * 100 
            : 0;
        
        // Combine all analytics data
        $analytics = [
            'total_paid' => $stats['total_amount_paid'],
            'total_paid_growth' => $totalPaidGrowth,
            'active_users' => $activeUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'avg_rebate_amount' => $avgRebateAmount,
            'total_rebates_count' => $stats['approved'],
            'approval_rate' => $approvalRate,
            'daily_stats' => $this->getDailyStats($dateRange),
            'top_categories' => $this->getTopCategories($dateRange),
            'top_users' => $this->getTopUsers($dateRange),
            'tier_distribution' => $this->getTierDistribution(),
            'fraud_trends' => $this->getFraudTrends($dateRange)
        ];

        // Get programs for filter dropdown
        $programs = RebateProgram::where('is_active', true)->get();
        
        // Get categories for filter dropdown
        $categories = RebateCategory::where('is_active', true)->get();
        
        // Get top performing programs
        $topPrograms = RebateTransaction::join('rebate_programs', 'rebate_transactions.rebate_program_id', '=', 'rebate_programs.id')
            ->where('rebate_transactions.created_at', '>=', $currentPeriodStart)
            ->where('rebate_transactions.status', 'approved')
            ->selectRaw('rebate_programs.name as program_name,
                        COUNT(*) as rebate_count,
                        SUM(rebate_transactions.rebate_amount) as total_amount')
            ->groupBy('rebate_programs.id', 'rebate_programs.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        // Extract top users as separate variable for the view
        $topUsers = $analytics['top_users'];
        
        // Get status breakdown
        $statusBreakdown = [
            'approved' => [
                'count' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'approved')->count(),
                'amount' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'approved')->sum('rebate_amount')
            ],
            'pending' => [
                'count' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'pending')->count(),
                'amount' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'pending')->sum('rebate_amount')
            ],
            'rejected' => [
                'count' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'rejected')->count(),
                'amount' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                    ->where('status', 'rejected')->sum('rebate_amount')
            ]
        ];
        
        // Get fraud statistics (using available columns)
        $fraudStats = [
            'total_flags' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                ->where('status', 'flagged')->count(),
            'flagged_users' => User::whereHas('rebateTransactions', function($query) use ($currentPeriodStart) {
                $query->where('created_at', '>=', $currentPeriodStart)
                      ->where('status', 'flagged');
            })->count(),
            'blocked_amount' => RebateTransaction::where('created_at', '>=', $currentPeriodStart)
                ->where('status', 'rejected')
                ->whereNotNull('review_notes')
                ->sum('rebate_amount')
        ];

        // Prepare chart data for programs
        $programData = [
            'labels' => $topPrograms->pluck('program_name')->toArray(),
            'data' => $topPrograms->pluck('total_amount')->toArray()
        ];
        
        // Prepare main chart data for rebate trends
        $dailyStats = $analytics['daily_stats'];
        $chartData = [
            'labels' => $dailyStats->pluck('date')->map(function($date) {
                return date('M j', strtotime($date));
            })->toArray(),
            'volume' => $dailyStats->pluck('count')->toArray(),
            'amount' => $dailyStats->pluck('approved_amount')->toArray()
        ];

        // Get general settings for currency display
        $general = gs();

        return view('admin.rebate.analytics', compact('pageTitle', 'stats', 'analytics', 'dateRange', 'general', 'programs', 'topPrograms', 'categories', 'topUsers', 'statusBreakdown', 'fraudStats', 'programData', 'chartData'));
    }

    /**
     * Export rebate data
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        try {
            $format = $request->get('format');
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;

            $rebates = RebateTransaction::with(['user', 'rebateProgram'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->get();

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($rebates, $dateFrom, $dateTo);
                case 'excel':
                    // TODO: Implement Excel export
                    $notify[] = ['info', 'Excel export coming soon'];
                    return back()->withNotify($notify);
                case 'pdf':
                    // TODO: Implement PDF export
                    $notify[] = ['info', 'PDF export coming soon'];
                    return back()->withNotify($notify);
            }

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            $notify[] = ['error', 'Export failed. Please try again.'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Get daily statistics
     */
    protected function getDailyStats($days)
    {
        return RebateTransaction::selectRaw('DATE(created_at) as date, 
                                    COUNT(*) as count,
                                    SUM(CASE WHEN status = "approved" THEN rebate_amount ELSE 0 END) as approved_amount,
                                    COUNT(CASE WHEN status = "approved" THEN 1 END) as approved_count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get top categories
     */
    protected function getTopCategories($days)
    {
        return RebateTransaction::join('rebate_categories', 'rebate_transactions.rebate_category_id', '=', 'rebate_categories.id')
            ->where('rebate_transactions.created_at', '>=', now()->subDays($days))
            ->where('rebate_transactions.status', 'approved')
            ->selectRaw('rebate_categories.name as category,
                        COUNT(*) as rebate_count,
                        SUM(rebate_transactions.rebate_amount) as total_amount')
            ->groupBy('rebate_categories.id', 'rebate_categories.name')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top users
     */
    protected function getTopUsers($days)
    {
        return RebateTransaction::join('users', 'rebate_transactions.user_id', '=', 'users.id')
            ->where('rebate_transactions.created_at', '>=', now()->subDays($days))
            ->where('rebate_transactions.status', 'approved')
            ->selectRaw('users.id,
                        users.username,
                        users.email,
                        users.firstname,
                        users.lastname,
                        users.image,
                        COUNT(*) as rebate_count,
                        SUM(rebate_transactions.final_amount) as total_earned')
            ->groupBy('users.id', 'users.username', 'users.email', 'users.firstname', 'users.lastname', 'users.image')
            ->orderBy('total_earned', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get tier distribution
     */
    protected function getTierDistribution()
    {
        // This is a simplified version - in production you'd cache this
        $tiers = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];
        $distribution = [];

        foreach ($tiers as $tier) {
            // This would normally use a more efficient method
            $distribution[$tier] = User::active()->get()->filter(function($user) use ($tier) {
                return app(\App\Services\UserTierService::class)->getUserTier($user) === $tier;
            })->count();
        }

        return $distribution;
    }

    /**
     * Get fraud trends
     */
    protected function getFraudTrends($days)
    {
        return DB::table('fraud_logs')
            ->selectRaw('DATE(created_at) as date,
                        COUNT(*) as total_flags,
                        AVG(fraud_score) as avg_score,
                        COUNT(CASE WHEN fraud_score >= 70 THEN 1 END) as high_risk_count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($rebates, $dateFrom, $dateTo)
    {
        $filename = "rebates_{$dateFrom}_to_{$dateTo}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($rebates) {
            $output = fopen('php://output', 'w');
            
            // Headers
            fputcsv($output, [
                'ID', 'User', 'Type', 'Amount', 'Status', 
                'Program', 'Created At', 'Approved At'
            ]);

            // Data
            foreach ($rebates as $rebate) {
                fputcsv($output, [
                    $rebate->id,
                    $rebate->user->username ?? 'N/A',
                    ucwords(str_replace('_', ' ', $rebate->type)),
                    '$' . number_format($rebate->rebate_amount, 2),
                    ucfirst($rebate->status),
                    $rebate->rebateProgram->name ?? 'N/A',
                    $rebate->created_at->format('Y-m-d H:i:s'),
                    $rebate->approved_at ? $rebate->approved_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Show pending rebates for quick review
     */
    public function pending()
    {
        $pageTitle = 'Pending Rebates';
        
        $rebates = RebateTransaction::with(['user', 'rebateProgram', 'rebateCategory'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(getPaginate());

        // Get general settings for currency display
        $general = gs();
        
        // Set empty message for no results
        $emptyMessage = 'No pending rebate transactions found';

        return view('admin.rebate.pending', compact('pageTitle', 'rebates', 'general', 'emptyMessage'));
    }

    /**
     * Show high-risk rebates requiring review
     */
    public function highRisk()
    {
        $pageTitle = 'High Risk Rebates';
        
        // Query for high-risk rebates based on available columns
        $rebates = RebateTransaction::with(['user', 'rebateProgram', 'rebateCategory'])
            ->where(function($query) {
                $query->where('status', 'flagged')  // Flagged transactions
                      ->orWhere('status', 'rejected') // Rejected transactions for review
                      ->orWhereNotNull('review_notes'); // Transactions with review notes
            })
            ->orderBy('created_at', 'desc') // Most recent first for high-risk items
            ->paginate(getPaginate());

        // Get general settings for currency display
        $general = gs();
        
        // Set empty message for no results
        $emptyMessage = 'No high risk rebate transactions found';

        return view('admin.rebate.high_risk', compact('pageTitle', 'rebates', 'general', 'emptyMessage'));
    }

    /**
     * Reprocess a failed rebate
     */
    public function reprocess($id)
    {
        $rebate = RebateTransaction::findOrFail($id);

        if (!$rebate->product_upload) {
            $notify[] = ['error', 'No product upload found for this rebate'];
            return back()->withNotify($notify);
        }

        // Only allow reprocessing for certain statuses
        if (!in_array($rebate->status, ['pending', 'rejected', 'flagged', 'failed'])) {
            $notify[] = ['error', 'Only pending, flagged, rejected or failed rebates can be reprocessed'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction();
        try {
            // Store original rebate data for logging
            $originalStatus = $rebate->status;
            $originalAmount = $rebate->rebate_amount;

            // Reset the rebate status and clear processed data to allow reprocessing
            $rebate->update([
                'status' => 'pending',
                'processed_at' => null,
                'approved_at' => null,
                'rejected_at' => null,
                'review_notes' => null
            ]);

            // Update product upload status to allow reprocessing
            $rebate->product_upload->update([
                'status' => 'pending',
                'final_rebate_amount' => null,
                'admin_notes' => null,
                'verified_at' => null,
                'rewarded_at' => null
            ]);

            // Now reprocess the rebate using the calculator service directly
            $calculatorService = app(RebateCalculatorService::class);
            
            // Validate product upload has required data
            if (!$rebate->product_upload->rebate_program_id) {
                DB::rollBack();
                $notify[] = ['error', 'Product upload is missing rebate program information'];
                return back()->withNotify($notify);
            }

            if (!$rebate->product_upload->purchase_amount || $rebate->product_upload->purchase_amount <= 0) {
                DB::rollBack();
                $notify[] = ['error', 'Product upload has invalid purchase amount'];
                return back()->withNotify($notify);
            }

            // Log the product upload details for debugging
            Log::info('Reprocessing rebate', [
                'rebate_id' => $rebate->id,
                'product_upload_id' => $rebate->product_upload->id,
                'user_id' => $rebate->product_upload->user_id,
                'rebate_program_id' => $rebate->product_upload->rebate_program_id,
                'category_id' => $rebate->product_upload->rebate_category_id,
                'purchase_amount' => $rebate->product_upload->purchase_amount
            ]);
            
            // Ensure product upload has all necessary relationships loaded
            $rebate->product_upload->load(['user', 'rebateProgram']);
            
            $calculation = $calculatorService->calculateProductRebate($rebate->product_upload);

            if (!$calculation['eligible']) {
                // Log the full calculation result for debugging
                Log::warning('Rebate reprocessing - not eligible', [
                    'rebate_id' => $rebate->id,
                    'calculation' => $calculation
                ]);

                // Rebate not eligible, update status
                $rebate->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'review_notes' => 'Reprocessing failed: ' . $calculation['reason']
                ]);

                $rebate->product_upload->update([
                    'status' => 'rejected',
                    'admin_notes' => 'Reprocessing failed: ' . $calculation['reason']
                ]);

                DB::commit();
                $notify[] = ['error', 'Reprocessing failed: ' . $calculation['reason']];
            } else {
                // Update rebate with new calculation
                $rebate->update([
                    'rebate_amount' => $calculation['rebate_amount'],
                    'tier_multiplier' => $calculation['tier_multiplier'],
                    'final_amount' => $calculation['rebate_amount'],
                    'status' => 'pending',
                    'review_notes' => 'Reprocessed - Original status: ' . $originalStatus . ', Original amount: ' . $originalAmount
                ]);

                // Update product upload with new calculated rebate
                $rebate->product_upload->update([
                    'calculated_rebate' => $calculation['base_amount'],
                    'status' => 'pending'
                ]);

                DB::commit();
                $notify[] = ['success', 'Rebate reprocessed successfully. New amount: ' . showAmount($calculation['rebate_amount']) . ' ' . gs()->cur_text];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reprocessing error: ' . $e->getMessage(), [
                'rebate_id' => $id,
                'product_upload_id' => $rebate->product_upload->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            $notify[] = ['error', 'An error occurred during reprocessing'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Display rebate programs
     */
    public function programs(Request $request)
    {
        $pageTitle = 'Rebate Programs';

        $query = RebateProgram::withCount(['transactions as user_rebates_count'])
            ->withSum(['transactions as user_rebates_sum_rebate_amount'], 'rebate_amount')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $programs = $query->paginate(getPaginate());

        // Get statistics
        $stats = [
            'total_programs' => RebateProgram::count(),
            'active_programs' => RebateProgram::where('is_active', true)->count(),
            'inactive_programs' => RebateProgram::where('is_active', false)->count(),
            'total_rebates_processed' => RebateTransaction::where('status', 'approved')->count(),
        ];

        // Variables expected by the template
        $totalPrograms = $stats['total_programs'];
        $activePrograms = $stats['active_programs'];
        $totalUsers = User::whereHas('rebateTransactions')->count();
        $totalPaid = RebateTransaction::where('status', 'approved')->sum('rebate_amount');
        
        // Empty message for when no programs found
        $emptyMessage = 'No rebate programs found';
        
        // Get category statistics - simplified approach since the relationships are complex
        $categoryStats = RebateCategory::where('is_active', true)
            ->get()
            ->map(function($category) {
                $transactionQuery = RebateTransaction::where('rebate_category_id', $category->id);
                
                return (object) [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'programs_count' => 1, // Each category is linked to one program
                    'active_programs_count' => $category->program && $category->program->is_active ? 1 : 0,
                    'total_participants' => $transactionQuery->distinct('user_id')->count(),
                    'total_paid' => $transactionQuery->where('status', 'approved')->sum('final_amount'),
                    'avg_rate' => $category->rebate_rate
                ];
            })
            ->filter(function($stat) {
                return $stat->total_participants > 0; // Only show categories with participants
            });
        
        // Get general settings for currency display
        $general = gs();

        return view('admin.rebate.programs.index', compact('pageTitle', 'programs', 'stats', 'general', 'totalPrograms', 'activePrograms', 'totalUsers', 'totalPaid', 'emptyMessage', 'categoryStats'));
    }

    /**
     * Show form to create new program
     */
    public function createProgram()
    {
        $pageTitle = 'Create Rebate Program';
        
        // Get general settings for currency display
        $general = gs();
        
        // Get all active categories (with assignment status)
        try {
            $categories = RebateCategory::where('is_active', true)
                ->with('program')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading categories: ' . $e->getMessage());
            $categories = collect(); // Empty collection as fallback
        }
        
        return view('admin.rebate.programs.create', compact('pageTitle', 'general', 'categories'));
    }

    /**
     * Store new rebate program
     */
    public function storeProgram(Request $request)
    {
        // Filter out empty new categories before validation
        if ($request->has('new_categories')) {
            $filteredNewCategories = collect($request->new_categories)
                ->filter(function ($category) {
                    return !empty($category['name']);
                })
                ->values()
                ->toArray();
            
            $request->merge(['new_categories' => $filteredNewCategories]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_rate' => 'required|numeric|min:0|max:100',
            'minimum_amount' => 'required|numeric|min:0',
            'maximum_rebate' => 'nullable|numeric|min:0',
            'daily_limit' => 'nullable|numeric|min:0',
            'monthly_limit' => 'nullable|numeric|min:0',
            'manual_members_count' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:rebate_categories,id',
            'new_categories' => 'nullable|array',
            'new_categories.*.name' => 'required|string|max:255',
            'new_categories.*.rebate_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $program = new RebateProgram();
        $program->name = $request->name;
        $program->description = $request->description;
        $program->default_rate = $request->default_rate;
        $program->minimum_amount = $request->minimum_amount;
        $program->maximum_rebate = $request->maximum_rebate;
        $program->daily_limit = $request->daily_limit;
        $program->monthly_limit = $request->monthly_limit;
        $program->manual_members_count = $request->manual_members_count ?: null;
        $program->starts_at = $request->starts_at;
        $program->ends_at = $request->ends_at;
        $program->is_active = $request->is_active ? true : false;
        $program->settings = json_encode([
            'auto_approval' => $request->boolean('settings.auto_approval'),
            'tier_multiplier' => $request->boolean('settings.tier_multiplier'),
            'fraud_detection' => $request->boolean('settings.fraud_detection'),
            'email_notifications' => $request->boolean('settings.email_notifications'),
            'risk_threshold' => $request->input('settings.risk_threshold', 75),
            'require_receipt' => $request->boolean('require_receipt'),
        ]);
        $program->save();

        // Handle existing categories
        if ($request->categories) {
            foreach ($request->categories as $categoryId) {
                $category = RebateCategory::find($categoryId);
                if ($category && !$category->rebate_program_id) {
                    $category->update(['rebate_program_id' => $program->id]);
                }
            }
        }

        // Handle new categories
        if ($request->new_categories) {
            foreach ($request->new_categories as $newCategory) {
                if (!empty($newCategory['name'])) {
                    RebateCategory::create([
                        'rebate_program_id' => $program->id,
                        'name' => $newCategory['name'],
                        'code' => strtoupper(str_replace([' ', '-'], '_', $newCategory['name'])),
                        'rebate_rate' => $newCategory['rebate_rate'] ?? $program->default_rate,
                        'minimum_amount' => $program->minimum_amount,
                        'is_active' => true
                    ]);
                }
            }
        }

        $notify[] = ['success', 'Rebate program created successfully'];
        return redirect()->route('admin.rebate.programs.index')->withNotify($notify);
    }

    /**
     * Show specific program
     */
    public function showProgram($id)
    {
        $pageTitle = 'Program Details';
        $program = RebateProgram::withCount(['transactions'])->findOrFail($id);
        
        // Get comprehensive program statistics
        $stats = [
            'total_transactions' => RebateTransaction::where('rebate_program_id', $id)->count(),
            'approved_transactions' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'approved')->count(),
            'pending_transactions' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'pending')->count(),
            'rejected_transactions' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'rejected')->count(),
            'total_paid' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'approved')->sum('final_amount'),
            'total_pending' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'pending')->sum('final_amount'),
            'avg_rebate' => RebateTransaction::where('rebate_program_id', $id)->where('status', 'approved')->avg('final_amount'),
            'unique_users' => RebateTransaction::where('rebate_program_id', $id)->distinct('user_id')->count('user_id'),
        ];
        
        // Get recent rebates for this program
        $recentRebates = DB::table('rebate_transactions')
            ->join('users', 'rebate_transactions.user_id', '=', 'users.id')
            ->where('rebate_transactions.rebate_program_id', $id)
            ->select([
                'rebate_transactions.*',
                'users.username',
                'users.firstname',
                'users.lastname'
            ])
            ->orderBy('rebate_transactions.created_at', 'desc')
            ->limit(10)
            ->get();

        // Get general settings for currency display
        $general = gs();

        return view('admin.rebate.programs.show', compact('pageTitle', 'program', 'recentRebates', 'stats', 'general'));
    }

    /**
     * Show form to edit program
     */
    public function editProgram($id)
    {
        $pageTitle = 'Edit Rebate Program';
        $program = RebateProgram::with(['categories'])->findOrFail($id);
        
        // Get general settings for currency display
        $general = gs();
        
        // Get all active categories (with assignment status)
        try {
            $allCategories = RebateCategory::where('is_active', true)
                ->with('program')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading categories: ' . $e->getMessage());
            $allCategories = collect(); // Empty collection as fallback
        }
        
        return view('admin.rebate.programs.edit', compact('pageTitle', 'program', 'general', 'allCategories'));
    }

    /**
     * Update rebate program
     */
    public function updateProgram(Request $request, $id)
    {
        // Filter out empty new categories before validation
        if ($request->has('new_categories')) {
            $filteredNewCategories = collect($request->new_categories)
                ->filter(function ($category) {
                    return !empty($category['name']);
                })
                ->values()
                ->toArray();
            
            $request->merge(['new_categories' => $filteredNewCategories]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_rate' => 'required|numeric|min:0|max:100',
            'minimum_amount' => 'required|numeric|min:0',
            'maximum_rebate' => 'nullable|numeric|min:0',
            'daily_limit' => 'nullable|numeric|min:0',
            'monthly_limit' => 'nullable|numeric|min:0',
            'manual_members_count' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:rebate_categories,id',
            'new_categories' => 'nullable|array',
            'new_categories.*.name' => 'required|string|max:255',
            'new_categories.*.rebate_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $program = RebateProgram::findOrFail($id);
        $program->name = $request->name;
        $program->description = $request->description;
        $program->default_rate = $request->default_rate;
        $program->minimum_amount = $request->minimum_amount;
        $program->maximum_rebate = $request->maximum_rebate;
        $program->daily_limit = $request->daily_limit;
        $program->monthly_limit = $request->monthly_limit;
        $program->manual_members_count = $request->manual_members_count ?: null;
        $program->starts_at = $request->starts_at;
        $program->ends_at = $request->ends_at;
        $program->is_active = $request->is_active ? true : false;
        $program->settings = json_encode([
            'auto_approval' => $request->boolean('settings.auto_approval'),
            'tier_multiplier' => $request->boolean('settings.tier_multiplier'),
            'fraud_detection' => $request->boolean('settings.fraud_detection'),
            'email_notifications' => $request->boolean('settings.email_notifications'),
            'risk_threshold' => $request->input('settings.risk_threshold', 75),
            'require_receipt' => $request->boolean('require_receipt'),
        ]);
        $program->save();

        // Handle category assignments
        $selectedCategories = $request->categories ?? [];
        
        // Since rebate_program_id cannot be null due to foreign key constraint,
        // we need to handle category reassignment carefully
        
        // Get all categories that belong to other programs but were selected for this program
        if (!empty($selectedCategories)) {
            // Move selected categories to this program
            RebateCategory::whereIn('id', $selectedCategories)
                ->update(['rebate_program_id' => $program->id]);
        }
        
        // For categories that were previously assigned to this program but are no longer selected,
        // we'll leave them as they are or could assign them to a default program
        // This prevents foreign key constraint violations

        // Handle new categories
        if ($request->new_categories) {
            foreach ($request->new_categories as $newCategory) {
                if (!empty($newCategory['name'])) {
                    RebateCategory::create([
                        'rebate_program_id' => $program->id,
                        'name' => $newCategory['name'],
                        'code' => strtoupper(str_replace([' ', '-'], '_', $newCategory['name'])),
                        'rebate_rate' => $newCategory['rebate_rate'] ?? $program->default_rate,
                        'minimum_amount' => $program->minimum_amount,
                        'is_active' => true
                    ]);
                }
            }
        }

        $notify[] = ['success', 'Rebate program updated successfully'];
        return redirect()->route('admin.rebate.programs.index')->withNotify($notify);
    }

    /**
     * Delete rebate program
     */
    public function destroyProgram($id)
    {
        $program = RebateProgram::findOrFail($id);
        
        // Check if program has associated rebates
        $hasRebates = DB::table('rebate_transactions')->where('rebate_program_id', $id)->exists();
        
        if ($hasRebates) {
            // Soft delete to preserve data integrity
            $program->delete();
            $notify[] = ['success', 'Rebate program archived successfully'];
        } else {
            // Hard delete if no associated data
            $program->forceDelete();
            $notify[] = ['success', 'Rebate program deleted successfully'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Toggle program status
     */
    public function toggleProgramStatus($id)
    {
        $program = RebateProgram::findOrFail($id);
        $program->is_active = !$program->is_active;
        $program->save();

        $status = $program->is_active ? 'activated' : 'deactivated';
        $notify[] = ['success', "Program {$status} successfully"];
        
        return back()->withNotify($notify);
    }

    /**
     * Show rebate system settings
     */
    public function settings()
    {
        $pageTitle = 'Rebate System Settings';
        
        // Get current rebate system settings from general settings
        $general = gs();
        
        // Get default rebate settings using helper function
        $defaultSettings = getRebateSettings();
        
        // Get rebate statistics for dashboard
        $stats = [
            'total_rebates_today' => RebateTransaction::whereDate('created_at', today())->count(),
            'pending_rebates' => RebateTransaction::where('status', 'pending')->count(),
            'total_amount_paid_today' => RebateTransaction::whereDate('created_at', today())
                ->where('status', 'approved')->sum('final_amount'),
            'fraud_flags_today' => RebateTransaction::whereDate('created_at', today())
                ->where('status', 'flagged')->count(),
        ];

        return view('admin.rebate.settings', compact('pageTitle', 'defaultSettings', 'stats', 'general'));
    }

    /**
     * Update rebate system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            // System settings (boolean fields are nullable since unchecked checkboxes don't send values)
            'system.enabled' => 'nullable|boolean',
            'system.auto_approval' => 'nullable|boolean',
            'system.auto_approval_limit' => 'required|numeric|min:0',
            'system.daily_limit_per_user' => 'required|numeric|min:0',
            'system.monthly_limit_per_user' => 'required|numeric|min:0',
            'system.minimum_rebate_amount' => 'required|numeric|min:0',
            'system.maximum_rebate_amount' => 'required|numeric|min:0',
            
            // Tier settings
            'tiers.enabled' => 'nullable|boolean',
            'tiers.bronze_threshold' => 'required|numeric|min:0',
            'tiers.silver_threshold' => 'required|numeric|min:0',
            'tiers.gold_threshold' => 'required|numeric|min:0',
            'tiers.platinum_threshold' => 'required|numeric|min:0',  
            'tiers.diamond_threshold' => 'required|numeric|min:0',
            'tiers.bronze_multiplier' => 'required|numeric|min:0.1|max:10',
            'tiers.silver_multiplier' => 'required|numeric|min:0.1|max:10',
            'tiers.gold_multiplier' => 'required|numeric|min:0.1|max:10',
            'tiers.platinum_multiplier' => 'required|numeric|min:0.1|max:10',
            'tiers.diamond_multiplier' => 'required|numeric|min:0.1|max:10',
            
            // Fraud settings
            'fraud.enabled' => 'nullable|boolean',
            'fraud.fraud_score_threshold' => 'required|integer|min:1|max:100',
            'fraud.max_daily_uploads' => 'required|integer|min:1',
            'fraud.max_rapid_uploads' => 'required|integer|min:1',
            'fraud.velocity_threshold' => 'required|integer|min:1',
            'fraud.ip_sharing_limit' => 'required|integer|min:1',
            'fraud.duplicate_detection' => 'nullable|boolean',
            
            // Notification settings
            'notifications.email_on_approval' => 'nullable|boolean',
            'notifications.email_on_rejection' => 'nullable|boolean',
            'notifications.email_on_tier_upgrade' => 'nullable|boolean',
            'notifications.admin_notification_threshold' => 'required|numeric|min:0',
        ]);

        try {
            $settings = [
                'system' => [
                    'enabled' => $request->boolean('system.enabled'),
                    'auto_approval' => $request->boolean('system.auto_approval'),
                    'auto_approval_limit' => $request->input('system.auto_approval_limit'),
                    'daily_limit_per_user' => $request->input('system.daily_limit_per_user'),
                    'monthly_limit_per_user' => $request->input('system.monthly_limit_per_user'),
                    'minimum_rebate_amount' => $request->input('system.minimum_rebate_amount'),
                    'maximum_rebate_amount' => $request->input('system.maximum_rebate_amount'),
                ],
                'tiers' => [
                    'enabled' => $request->boolean('tiers.enabled'),
                    'bronze_threshold' => $request->input('tiers.bronze_threshold'),
                    'silver_threshold' => $request->input('tiers.silver_threshold'),
                    'gold_threshold' => $request->input('tiers.gold_threshold'),
                    'platinum_threshold' => $request->input('tiers.platinum_threshold'),
                    'diamond_threshold' => $request->input('tiers.diamond_threshold'),
                    'bronze_multiplier' => $request->input('tiers.bronze_multiplier'),
                    'silver_multiplier' => $request->input('tiers.silver_multiplier'),
                    'gold_multiplier' => $request->input('tiers.gold_multiplier'),
                    'platinum_multiplier' => $request->input('tiers.platinum_multiplier'),
                    'diamond_multiplier' => $request->input('tiers.diamond_multiplier'),
                ],
                'fraud' => [
                    'enabled' => $request->boolean('fraud.enabled'),
                    'fraud_score_threshold' => $request->input('fraud.fraud_score_threshold'),
                    'max_daily_uploads' => $request->input('fraud.max_daily_uploads'),
                    'max_rapid_uploads' => $request->input('fraud.max_rapid_uploads'),
                    'velocity_threshold' => $request->input('fraud.velocity_threshold'),
                    'ip_sharing_limit' => $request->input('fraud.ip_sharing_limit'),
                    'duplicate_detection' => $request->boolean('fraud.duplicate_detection'),
                ],
                'notifications' => [
                    'email_on_approval' => $request->boolean('notifications.email_on_approval'),
                    'email_on_rejection' => $request->boolean('notifications.email_on_rejection'),
                    'email_on_tier_upgrade' => $request->boolean('notifications.email_on_tier_upgrade'),
                    'admin_notification_threshold' => $request->input('notifications.admin_notification_threshold'),
                ]
            ];

            // Update settings in general settings
            $general = gs();
            $general->rebate_settings = json_encode($settings);
            $general->save();

            $notify[] = ['success', 'Rebate system settings updated successfully'];

        } catch (\Exception $e) {
            Log::error('Rebate settings update error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while updating settings'];
        }

        return back()->withNotify($notify);
    }
}
