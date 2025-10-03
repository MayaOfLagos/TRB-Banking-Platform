<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to optimize rebate system performance
     */
    public function up(): void
    {
        // Create analytics cache table only (skip indexes that may already exist)
        if (!Schema::hasTable('rebate_analytics_cache')) {
            DB::statement("
                CREATE TABLE rebate_analytics_cache (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    total_transactions INT DEFAULT 0,
                    total_earned DECIMAL(10,2) DEFAULT 0.00,
                    pending_amount DECIMAL(10,2) DEFAULT 0.00,
                    approved_count INT DEFAULT 0,
                    rejected_count INT DEFAULT 0,
                    current_tier INT DEFAULT 1,
                    last_transaction_date TIMESTAMP NULL,
                    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_user_analytics (user_id),
                    INDEX idx_tier_earnings (current_tier, total_earned),
                    INDEX idx_last_updated (last_updated)
                ) ENGINE=InnoDB
            ");
        }
        
        // Create stored procedure for updating analytics cache
        DB::statement("DROP PROCEDURE IF EXISTS UpdateRebateAnalytics");
        DB::statement("
            CREATE PROCEDURE UpdateRebateAnalytics(IN p_user_id INT)
            BEGIN
                DECLARE v_total_transactions INT DEFAULT 0;
                DECLARE v_total_earned DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_pending_amount DECIMAL(10,2) DEFAULT 0.00;
                DECLARE v_approved_count INT DEFAULT 0;
                DECLARE v_rejected_count INT DEFAULT 0;
                DECLARE v_current_tier INT DEFAULT 1;
                DECLARE v_last_transaction_date TIMESTAMP;
                
                -- Calculate metrics from rebate_transactions
                SELECT 
                    COUNT(*),
                    COALESCE(SUM(CASE WHEN status = 'approved' THEN rebate_amount ELSE 0 END), 0),
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN rebate_amount ELSE 0 END), 0),
                    COUNT(CASE WHEN status = 'approved' THEN 1 END),
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END),
                    MAX(created_at)
                INTO 
                    v_total_transactions,
                    v_total_earned,
                    v_pending_amount,
                    v_approved_count,
                    v_rejected_count,
                    v_last_transaction_date
                FROM rebate_transactions 
                WHERE user_id = p_user_id;
                
                -- Calculate tier based on earnings
                SET v_current_tier = CASE 
                    WHEN v_total_earned >= 10000 THEN 5
                    WHEN v_total_earned >= 5000 THEN 4
                    WHEN v_total_earned >= 2000 THEN 3
                    WHEN v_total_earned >= 500 THEN 2
                    ELSE 1
                END;
                
                -- Update or insert analytics
                INSERT INTO rebate_analytics_cache (
                    user_id, total_transactions, total_earned, pending_amount,
                    approved_count, rejected_count, current_tier, last_transaction_date
                ) VALUES (
                    p_user_id, v_total_transactions, v_total_earned, v_pending_amount,
                    v_approved_count, v_rejected_count, v_current_tier, v_last_transaction_date
                ) ON DUPLICATE KEY UPDATE
                    total_transactions = v_total_transactions,
                    total_earned = v_total_earned,
                    pending_amount = v_pending_amount,
                    approved_count = v_approved_count,
                    rejected_count = v_rejected_count,  
                    current_tier = v_current_tier,
                    last_transaction_date = v_last_transaction_date,
                    last_updated = CURRENT_TIMESTAMP;
            END
        ");
    }
    
    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Drop stored procedure
        DB::statement("DROP PROCEDURE IF EXISTS UpdateRebateAnalytics");
        
        // Drop analytics cache table
        DB::statement("DROP TABLE IF EXISTS rebate_analytics_cache");
        
        // Clean up created resources only
    }
    
    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }
};