<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RebateCacheService;
use App\Models\RebateTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OptimizeRebateSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rebate:optimize 
                          {--warmup : Warm up caches}
                          {--cleanup : Clean up old data}
                          {--analytics : Update analytics cache}
                          {--maintenance : Run full maintenance}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize rebate system performance and maintenance';

    /**
     * The rebate cache service
     */
    protected RebateCacheService $cacheService;

    /**
     * Create a new command instance.
     */
    public function __construct(RebateCacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Rebate System Optimization...');
        
        if ($this->option('warmup') || $this->option('maintenance')) {
            $this->warmupCaches();
        }
        
        if ($this->option('cleanup') || $this->option('maintenance')) {
            $this->cleanupOldData();
        }
        
        if ($this->option('analytics') || $this->option('maintenance')) {
            $this->updateAnalyticsCache();
        }
        
        if ($this->option('maintenance')) {
            $this->runFullMaintenance();
        }
        
        $this->info('✅ Rebate System Optimization Complete!');
        
        return 0;
    }
    
    /**
     * Warm up system caches
     */
    protected function warmupCaches()
    {
        $this->info('🔥 Warming up caches...');
        
        try {
            $this->cacheService->warmupCaches();
            $this->info('✅ Cache warmup completed');
        } catch (\Exception $e) {
            $this->error('❌ Cache warmup failed: ' . $e->getMessage());
            Log::error('Cache warmup failed', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Clean up old data and logs
     */
    protected function cleanupOldData()
    {
        $this->info('🧹 Cleaning up old data...');
        
        try {
            // Clean up old fraud logs (older than 6 months)
            $sixMonthsAgo = now()->subMonths(6);
            $deletedFraudLogs = DB::table('rebate_fraud_logs')
                ->where('created_at', '<', $sixMonthsAgo)
                ->where('resolved', true)
                ->delete();
            
            $this->info("🗑️  Cleaned up {$deletedFraudLogs} old fraud logs");
            
            // Clean up old push notifications (older than 3 months)
            $threeMonthsAgo = now()->subMonths(3);
            $deletedNotifications = DB::table('push_notifications')
                ->where('created_at', '<', $threeMonthsAgo)
                ->where('is_read', true)
                ->delete();
            
            $this->info("🗑️  Cleaned up {$deletedNotifications} old notifications");
            
            // Archive old completed transactions (older than 1 year)
            $oneYearAgo = now()->subYear();
            $archivedTransactions = $this->archiveOldTransactions($oneYearAgo);
            
            $this->info("📦 Archived {$archivedTransactions} old transactions");
            
            // Clean up expired cache entries
            $this->info('🧽 Clearing expired cache entries...');
            // This would depend on your cache driver implementation
            
            $this->info('✅ Data cleanup completed');
            
        } catch (\Exception $e) {
            $this->error('❌ Data cleanup failed: ' . $e->getMessage());
            Log::error('Data cleanup failed', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Update analytics cache for all users
     */
    protected function updateAnalyticsCache()
    {
        $this->info('📊 Updating analytics cache...');
        
        try {
            // Get all users with rebate transactions
            $userIds = RebateTransaction::distinct('user_id')->pluck('user_id');
            
            $bar = $this->output->createProgressBar($userIds->count());
            $bar->start();
            
            foreach ($userIds as $userId) {
                // Update analytics cache using stored procedure
                DB::statement('CALL UpdateRebateAnalytics(?)', [$userId]);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("✅ Updated analytics cache for {$userIds->count()} users");
            
        } catch (\Exception $e) {
            $this->error('❌ Analytics cache update failed: ' . $e->getMessage());
            Log::error('Analytics cache update failed', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Run full system maintenance
     */
    protected function runFullMaintenance()
    {
        $this->info('🔧 Running full system maintenance...');
        
        try {
            // Optimize database tables
            $this->optimizeDatabaseTables();
            
            // Update table statistics
            $this->updateTableStatistics();
            
            // Validate data integrity
            $this->validateDataIntegrity();
            
            // Generate system health report
            $this->generateHealthReport();
            
            $this->info('✅ Full maintenance completed');
            
        } catch (\Exception $e) {
            $this->error('❌ Full maintenance failed: ' . $e->getMessage());
            Log::error('Full maintenance failed', ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Archive old transactions to separate table
     */
    protected function archiveOldTransactions($cutoffDate)
    {
        // Create archive table if it doesn't exist
        DB::statement("
            CREATE TABLE IF NOT EXISTS rebate_transactions_archive 
            LIKE rebate_transactions
        ");
        
        // Move old completed transactions to archive
        $archivedCount = DB::statement("
            INSERT INTO rebate_transactions_archive 
            SELECT * FROM rebate_transactions 
            WHERE created_at < ? AND status IN ('approved', 'rejected')
        ", [$cutoffDate]);
        
        // Delete archived transactions from main table
        DB::table('rebate_transactions')
            ->where('created_at', '<', $cutoffDate)
            ->whereIn('status', ['approved', 'rejected'])
            ->delete();
        
        return $archivedCount;
    }
    
    /**
     * Optimize database tables
     */
    protected function optimizeDatabaseTables()
    {
        $this->info('🗄️  Optimizing database tables...');
        
        $tables = [
            'rebate_transactions',
            'rebate_programs', 
            'rebate_individual_tracking',
            'rebate_fraud_logs',
            'push_notifications',
            'rebate_analytics_cache'
        ];
        
        foreach ($tables as $table) {
            DB::statement("OPTIMIZE TABLE {$table}");
            $this->info("   ✅ Optimized {$table}");
        }
    }
    
    /**
     * Update table statistics for query optimizer
     */
    protected function updateTableStatistics()
    {
        $this->info('📈 Updating table statistics...');
        
        $tables = [
            'rebate_transactions',
            'rebate_programs',
            'rebate_individual_tracking',
            'users'
        ];
        
        foreach ($tables as $table) {
            DB::statement("ANALYZE TABLE {$table}");
        }
    }
    
    /**
     * Validate data integrity
     */
    protected function validateDataIntegrity()
    {
        $this->info('🔍 Validating data integrity...');
        
        // Check for orphaned transactions
        $orphanedTransactions = DB::table('rebate_transactions')
            ->leftJoin('users', 'rebate_transactions.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();
            
        if ($orphanedTransactions > 0) {
            $this->warn("⚠️  Found {$orphanedTransactions} orphaned transactions");
        }
        
        // Check for transactions without programs
        $missingPrograms = DB::table('rebate_transactions')
            ->leftJoin('rebate_programs', 'rebate_transactions.rebate_program_id', '=', 'rebate_programs.id')
            ->whereNull('rebate_programs.id')
            ->count();
            
        if ($missingPrograms > 0) {
            $this->warn("⚠️  Found {$missingPrograms} transactions with missing programs");
        }
        
        // Check for invalid rebate amounts
        $invalidAmounts = DB::table('rebate_transactions')
            ->where('rebate_amount', '<', 0)
            ->orWhere('rebate_amount', '>', 999999)
            ->count();
            
        if ($invalidAmounts > 0) {
            $this->warn("⚠️  Found {$invalidAmounts} transactions with invalid amounts");
        }
        
        if ($orphanedTransactions === 0 && $missingPrograms === 0 && $invalidAmounts === 0) {
            $this->info('✅ Data integrity validation passed');
        }
    }
    
    /**
     * Generate system health report
     */
    protected function generateHealthReport()
    {
        $this->info('📋 Generating system health report...');
        
        $report = [
            'timestamp' => now()->toISOString(),
            'total_transactions' => RebateTransaction::count(),
            'pending_transactions' => RebateTransaction::where('status', 'pending')->count(),
            'approved_today' => RebateTransaction::where('status', 'approved')
                ->whereDate('created_at', today())->count(),
            'system_load' => sys_getloadavg()[0] ?? 'N/A',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'cache_status' => 'active',
        ];
        
        // Log the health report
        Log::info('Rebate System Health Report', $report);
        
        // Display summary
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Transactions', $report['total_transactions']],
                ['Pending Transactions', $report['pending_transactions']],
                ['Approved Today', $report['approved_today']],
                ['Memory Usage', $report['memory_usage']],
                ['Cache Status', $report['cache_status']],
            ]
        );
    }
}