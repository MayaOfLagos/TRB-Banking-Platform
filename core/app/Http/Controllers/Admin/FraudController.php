<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FraudDetectionService;
use App\Models\User;
use App\Models\ProductUpload;
use App\Models\UserRebate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FraudController extends Controller
{
    protected $fraudDetectionService;

    public function __construct(FraudDetectionService $fraudDetectionService)
    {
        $this->fraudDetectionService = $fraudDetectionService;
    }

    /**
     * Display fraud dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = 'Fraud Detection Dashboard';

        $dateRange = $request->get('range', 30);
        
        // Get fraud statistics
        $stats = $this->fraudDetectionService->generateFraudReport($dateRange);
        
        // Get recent high-risk activities
        $highRiskLogs = DB::table('fraud_logs')
            ->leftJoin('users', 'fraud_logs.user_id', '=', 'users.id')
            ->leftJoin('product_uploads', 'fraud_logs.product_upload_id', '=', 'product_uploads.id')
            ->where('fraud_logs.fraud_score', '>=', 50)
            ->where('fraud_logs.created_at', '>=', now()->subDays($dateRange))
            ->select(
                'fraud_logs.*',
                'users.username',
                'users.email',
                'product_uploads.product_name'
            )
            ->orderBy('fraud_logs.created_at', 'desc')
            ->limit(20)
            ->get();

        // Get suspicious IP addresses
        $suspiciousIPs = DB::table('fraud_logs')
            ->selectRaw('ip_address, COUNT(DISTINCT user_id) as user_count, AVG(fraud_score) as avg_score, MAX(fraud_score) as max_score')
            ->where('created_at', '>=', now()->subDays($dateRange))
            ->groupBy('ip_address')
            ->having('user_count', '>', 3)
            ->orHaving('max_score', '>', 70)
            ->orderBy('user_count', 'desc')
            ->get();

        return view('admin.fraud.index', compact('pageTitle', 'stats', 'highRiskLogs', 'suspiciousIPs', 'dateRange'));
    }

    /**
     * Show detailed fraud analysis for a user
     */
    public function analyzeUser($userId)
    {
        $pageTitle = 'User Fraud Analysis';
        $user = User::findOrFail($userId);

        // Get user's fraud history
        $fraudHistory = DB::table('fraud_logs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's rebate statistics
        $rebateStats = [
            'total_rebates' => $user->rebates()->count(),
            'approved_rebates' => $user->rebates()->where('status', 'approved')->count(),
            'rejected_rebates' => $user->rebates()->where('status', 'rejected')->count(),
            'pending_rebates' => $user->rebates()->where('status', 'pending')->count(),
            'total_earned' => $user->rebates()->where('status', 'approved')->sum('rebate_amount'),
            'success_rate' => $this->calculateSuccessRate($user)
        ];

        // Get user's upload patterns
        $uploadPatterns = $this->analyzeUploadPatterns($user);

        // Run current fraud validation
        $currentValidation = $this->fraudDetectionService->validateUserForRebate($user);

        return view('admin.fraud.user_analysis', compact(
            'pageTitle', 'user', 'fraudHistory', 'rebateStats', 
            'uploadPatterns', 'currentValidation'
        ));
    }

    /**
     * Show fraud alerts
     */
    public function alerts()
    {
        $pageTitle = 'Fraud Alerts';

        // Get high-priority alerts
        $alerts = DB::table('fraud_logs')
            ->leftJoin('users', 'fraud_logs.user_id', '=', 'users.id')
            ->leftJoin('product_uploads', 'fraud_logs.product_upload_id', '=', 'product_uploads.id')
            ->where('fraud_logs.fraud_score', '>=', 70)
            ->whereNull('fraud_logs.reviewed_at')
            ->select(
                'fraud_logs.*',
                'users.username',
                'users.email',
                'product_uploads.product_name'
            )
            ->orderBy('fraud_logs.fraud_score', 'desc')
            ->paginate(getPaginate());

        return view('admin.fraud.alerts', compact('pageTitle', 'alerts'));
    }

    /**
     * Review fraud alert
     */
    public function reviewAlert(Request $request, $logId)
    {
        $request->validate([
            'action' => 'required|in:approve,flag,block,dismiss',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::table('fraud_logs')
                ->where('id', $logId)
                ->update([
                    'action_taken' => $request->action,
                    'reviewed_at' => now(),
                    'reviewed_by' => auth()->guard('admin')->id(),
                    'review_notes' => $request->notes
                ]);

            // Take additional action based on review decision
            $fraudLog = DB::table('fraud_logs')->find($logId);
            
            if ($request->action === 'block' && $fraudLog) {
                $this->blockUser($fraudLog->user_id, $request->notes);
            }

            $notify[] = ['success', 'Fraud alert reviewed successfully'];

        } catch (\Exception $e) {
            Log::error('Fraud alert review error: ' . $e->getMessage());
            $notify[] = ['error', 'An error occurred while reviewing the alert'];
        }

        return back()->withNotify($notify);
    }

    /**
     * Block user for fraud
     */
    protected function blockUser($userId, $reason = null)
    {
        $user = User::find($userId);
        if ($user) {
            $user->update([
                'status' => 0, // Blocked status
                'ban_reason' => $reason ?: 'Fraud detection'
            ]);

            // Reject all pending rebates
            UserRebate::where('user_id', $userId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'User blocked for fraud',
                    'rejected_at' => now(),
                    'rejected_by' => auth()->guard('admin')->id()
                ]);

            Log::info('User blocked for fraud', [
                'user_id' => $userId,
                'reason' => $reason,
                'blocked_by' => auth()->guard('admin')->id()
            ]);
        }
    }

    /**
     * Show fraud trends and analytics
     */
    public function trends(Request $request)
    {
        $pageTitle = 'Fraud Trends & Analytics';
        $dateRange = $request->get('range', 30);

        // Get fraud trends over time
        $trends = DB::table('fraud_logs')
            ->selectRaw('DATE(created_at) as date, 
                        COUNT(*) as total_flags,
                        AVG(fraud_score) as avg_score,
                        COUNT(CASE WHEN fraud_score >= 70 THEN 1 END) as high_risk,
                        COUNT(CASE WHEN fraud_score >= 40 AND fraud_score < 70 THEN 1 END) as medium_risk')
            ->where('created_at', '>=', now()->subDays($dateRange))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Get most common fraud flags
        $commonFlags = DB::table('fraud_logs')
            ->selectRaw('flags, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($dateRange))
            ->groupBy('flags')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $flags = json_decode($item->flags, true);
                return [
                    'flags' => implode(', ', $flags ?: []),
                    'count' => $item->count
                ];
            });

        // IP analysis
        $ipAnalysis = DB::table('fraud_logs')
            ->selectRaw('ip_address, 
                        COUNT(DISTINCT user_id) as unique_users,
                        COUNT(*) as total_logs,
                        AVG(fraud_score) as avg_score,
                        MAX(fraud_score) as max_score')
            ->where('created_at', '>=', now()->subDays($dateRange))
            ->groupBy('ip_address')
            ->having('unique_users', '>', 2)
            ->orderBy('unique_users', 'desc')
            ->get();

        return view('admin.fraud.trends', compact(
            'pageTitle', 'trends', 'commonFlags', 'ipAnalysis', 'dateRange'
        ));
    }

    /**
     * Whitelist/Blacklist IP addresses
     */
    public function manageIPs()
    {
        $pageTitle = 'IP Address Management';

        // This would typically use a dedicated table for IP management
        $suspiciousIPs = DB::table('fraud_logs')
            ->selectRaw('ip_address, COUNT(DISTINCT user_id) as user_count, MAX(fraud_score) as max_score')
            ->groupBy('ip_address')
            ->having('user_count', '>', 5)
            ->orHaving('max_score', '>', 80)
            ->orderBy('max_score', 'desc')
            ->get();

        return view('admin.fraud.ip_management', compact('pageTitle', 'suspiciousIPs'));
    }

    /**
     * Generate fraud report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:html,pdf,csv'
        ]);

        try {
            $dateFrom = $request->date_from;
            $dateTo = $request->date_to;
            $format = $request->get('format');

            $report = $this->generateFraudReportData($dateFrom, $dateTo);

            switch ($format) {
                case 'csv':
                    return $this->exportReportToCsv($report, $dateFrom, $dateTo);
                case 'pdf':
                    // TODO: Implement PDF export
                    $notify[] = ['info', 'PDF export coming soon'];
                    return back()->withNotify($notify);
                default:
                    return view('admin.fraud.report', compact('report', 'dateFrom', 'dateTo'));
            }

        } catch (\Exception $e) {
            Log::error('Fraud report generation error: ' . $e->getMessage());
            $notify[] = ['error', 'Report generation failed'];
            return back()->withNotify($notify);
        }
    }

    /**
     * Calculate user success rate
     */
    protected function calculateSuccessRate(User $user)
    {
        $total = $user->rebates()->count();
        if ($total === 0) return 0;

        $approved = $user->rebates()->where('status', 'approved')->count();
        return round(($approved / $total) * 100, 2);
    }

    /**
     * Analyze user upload patterns
     */
    protected function analyzeUploadPatterns(User $user)
    {
        $uploads = ProductUpload::where('user_id', $user->id)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->get();

        $hourlyPattern = array_fill(0, 24, 0);
        foreach ($uploads as $upload) {
            $hourlyPattern[$upload->hour] = $upload->count;
        }

        return [
            'hourly_distribution' => $hourlyPattern,
            'peak_hours' => array_keys($hourlyPattern, max($hourlyPattern)),
            'total_uploads' => array_sum($hourlyPattern),
            'night_uploads' => array_sum(array_slice($hourlyPattern, 0, 6)) + 
                             array_sum(array_slice($hourlyPattern, 22, 2))
        ];
    }

    /**
     * Generate comprehensive fraud report data
     */
    protected function generateFraudReportData($dateFrom, $dateTo)
    {
        return [
            'summary' => [
                'total_logs' => DB::table('fraud_logs')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'high_risk_count' => DB::table('fraud_logs')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->where('fraud_score', '>=', 70)->count(),
                'blocked_users' => User::where('status', 0)
                    ->whereBetween('updated_at', [$dateFrom, $dateTo])->count()
            ],
            'trends' => DB::table('fraud_logs')
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, AVG(fraud_score) as avg_score')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_flags' => DB::table('fraud_logs')
                ->selectRaw('flags, COUNT(*) as count')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('flags')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Export fraud report to CSV
     */
    protected function exportReportToCsv($report, $dateFrom, $dateTo)
    {
        $filename = "fraud_report_{$dateFrom}_to_{$dateTo}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($report) {
            $output = fopen('php://output', 'w');
            
            // Summary section
            fputcsv($output, ['FRAUD REPORT SUMMARY']);
            fputcsv($output, ['Total Logs', $report['summary']['total_logs']]);
            fputcsv($output, ['High Risk Count', $report['summary']['high_risk_count']]);
            fputcsv($output, ['Blocked Users', $report['summary']['blocked_users']]);
            fputcsv($output, []);

            // Trends section
            fputcsv($output, ['DAILY TRENDS']);
            fputcsv($output, ['Date', 'Count', 'Average Score']);
            foreach ($report['trends'] as $trend) {
                fputcsv($output, [$trend->date, $trend->count, round($trend->avg_score, 2)]);
            }

            fclose($output);
        }, 200, $headers);
    }
}
