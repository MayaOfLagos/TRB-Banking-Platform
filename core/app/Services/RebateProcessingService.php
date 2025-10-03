<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Models\ProductUpload;
use App\Models\RebateProgram;
use App\Constants\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessRebateJob;
use App\Events\RebateProcessed;
use App\Events\RebateRejected;
use App\Events\RebateApproved;
use Carbon\Carbon;

class RebateProcessingService
{
    protected $calculatorService;
    protected $fraudDetectionService;
    protected $tierService;

    public function __construct(
        RebateCalculatorService $calculatorService,
        FraudDetectionService $fraudDetectionService,
        UserTierService $tierService
    ) {
        $this->calculatorService = $calculatorService;
        $this->fraudDetectionService = $fraudDetectionService;
        $this->tierService = $tierService;
    }

    /**
     * Process rebate for product upload
     */
    public function processProductRebate(ProductUpload $productUpload, bool $async = true): array
    {
        try {
            if ($async) {
                // Queue for background processing
                ProcessRebateJob::dispatch($productUpload, 'product');
                
                return [
                    'success' => true,
                    'message' => 'Rebate queued for processing',
                    'queued' => true
                ];
            }

            return $this->processProductRebateSync($productUpload);

        } catch (\Exception $e) {
            Log::error('Rebate processing error: ' . $e->getMessage(), [
                'product_upload_id' => $productUpload->id,
                'user_id' => $productUpload->user_id
            ]);

            return [
                'success' => false,
                'message' => 'Processing error occurred',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process rebate synchronously
     */
    public function processProductRebateSync(ProductUpload $productUpload): array
    {
        DB::beginTransaction();

        try {
            // Check if rebate already processed
            $existingRebate = RebateTransaction::where('product_upload_id', $productUpload->id)->first();
            if ($existingRebate) {
                return [
                    'success' => false,
                    'message' => 'Rebate already processed for this upload',
                    'rebate_id' => $existingRebate->id
                ];
            }

            // Calculate rebate
            $calculation = $this->calculatorService->calculateProductRebate($productUpload);

            if (!$calculation['eligible']) {
                // Log rejection
                $this->logRebateRejection($productUpload, $calculation['reason']);
                
                DB::commit();
                return [
                    'success' => false,
                    'message' => $calculation['reason'],
                    'calculation' => $calculation
                ];
            }

            // Create rebate record
            $rebate = $this->createRebateRecord($productUpload, $calculation);

            // Determine if auto-approval is eligible
            $autoApprove = $this->shouldAutoApprove($productUpload, $calculation);

            if ($autoApprove) {
                $this->approveRebate($rebate);
            } else {
                // Mark for manual review
                $rebate->update([
                    'status' => 'pending',
                    'requires_review' => true,
                    'review_reason' => $this->getReviewReason($calculation)
                ]);
            }

            // Update product upload status
            $productUpload->update([
                'rebate_status' => $rebate->status,
                'rebate_amount' => $rebate->rebate_amount,
                'processed_at' => now()
            ]);

            DB::commit();

            // Fire events
            if ($autoApprove) {
                event(new RebateApproved($rebate));
            } else {
                event(new RebateProcessed($rebate));
            }

            return [
                'success' => true,
                'message' => $autoApprove ? 'Rebate approved automatically' : 'Rebate queued for review',
                'rebate' => $rebate,
                'auto_approved' => $autoApprove
            ];

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Rebate processing sync error: ' . $e->getMessage(), [
                'product_upload_id' => $productUpload->id
            ]);

            throw $e;
        }
    }

    /**
     * Process referral rebate
     */
    public function processReferralRebate(User $referrer, User $referee, string $action = 'signup'): array
    {
        try {
            DB::beginTransaction();

            // Check if referral rebate already processed
            $existingRebate = RebateTransaction::where('user_id', $referrer->id)
                ->where('reference_id', $referee->id)
                ->where('reference_type', 'referral_user')
                ->where('transaction_type', 'referral')
                ->first();

            if ($existingRebate) {
                DB::rollback();
                return [
                    'success' => false,
                    'message' => 'Referral rebate already processed'
                ];
            }

            // Calculate referral rebate
            $calculation = $this->calculatorService->calculateReferralRebate($referrer, $referee, $action);

            if (!$calculation['eligible']) {
                DB::rollback();
                return [
                    'success' => false,
                    'message' => $calculation['reason']
                ];
            }

            // Create referral rebate
            $rebate = UserRebate::create([
                'user_id' => $referrer->id,
                'rebate_program_id' => $calculation['program']->id,
                'type' => 'referral',
                'rebate_amount' => $calculation['rebate_amount'],
                'base_amount' => $calculation['base_amount'],
                'tier_multiplier' => $calculation['tier_multiplier'],
                'status' => 'approved', // Referrals auto-approved
                'referee_user_id' => $referee->id,
                'action_trigger' => $action,
                'approved_at' => now(),
                'approved_by' => 'system'
            ]);

            // Credit user account
            $this->creditUserAccount($referrer, $rebate);

            DB::commit();

            event(new RebateApproved($rebate));

            return [
                'success' => true,
                'message' => 'Referral rebate processed successfully',
                'rebate' => $rebate
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Referral rebate processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve a rebate
     */
    public function approveRebate(UserRebate $rebate, int $approvedBy = null): array
    {
        try {
            if ($rebate->status === 'approved') {
                return [
                    'success' => false,
                    'message' => 'Rebate already approved'
                ];
            }

            DB::beginTransaction();

            // Update rebate status
            $rebate->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $approvedBy ?? 'system'
            ]);

            // Credit user account
            $this->creditUserAccount($rebate->user, $rebate);

            // Check for tier upgrade
            $tierUpgrade = $this->tierService->checkAndUpgradeTier($rebate->user);

            DB::commit();

            event(new RebateApproved($rebate));

            return [
                'success' => true,
                'message' => 'Rebate approved successfully',
                'rebate' => $rebate,
                'tier_upgraded' => $tierUpgrade['upgraded'],
                'new_tier' => $tierUpgrade['new_tier'] ?? null
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Rebate approval error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Approve a rebate transaction
     */
    public function approveRebateTransaction(RebateTransaction $transaction, int $approvedBy = null): array
    {
        try {
            if ($transaction->status === 'approved') {
                return [
                    'success' => false,
                    'message' => 'Rebate transaction already approved'
                ];
            }

            DB::beginTransaction();

            // Update transaction status
            $transaction->update([
                'status' => 'approved',
                'approved_at' => now(),
                'processed_at' => now()
            ]);

            // Credit user account
            $this->creditUserAccountFromTransaction($transaction->user, $transaction);

            // Check for tier upgrade
            $tierUpgrade = $this->tierService->checkAndUpgradeTier($transaction->user);

            DB::commit();

            // Fire custom event since RebateApproved expects UserRebate
            Log::info('Rebate transaction approved', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->final_amount
            ]);

            return [
                'success' => true,
                'message' => 'Rebate transaction approved successfully',
                'transaction' => $transaction,
                'tier_upgraded' => $tierUpgrade['upgraded'],
                'new_tier' => $tierUpgrade['new_tier'] ?? null
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Rebate transaction approval error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reject a rebate transaction
     */
    public function rejectRebateTransaction(RebateTransaction $transaction, string $reason, int $rejectedBy = null): array
    {
        try {
            if ($transaction->status === 'rejected') {
                return [
                    'success' => false,
                    'message' => 'Rebate transaction already rejected'
                ];
            }

            DB::beginTransaction();

            // Update transaction status
            $transaction->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'review_notes' => $reason
            ]);

            // Log the rejection
            Log::info('Rebate transaction rejected', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'reason' => $reason,
                'rejected_by' => $rejectedBy
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Rebate transaction rejected successfully',
                'transaction' => $transaction
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Rebate transaction rejection error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reject a rebate
     */
    public function rejectRebate(UserRebate $rebate, string $reason, int $rejectedBy = null): array
    {
        try {
            if ($rebate->status === 'rejected') {
                return [
                    'success' => false,
                    'message' => 'Rebate already rejected'
                ];
            }

            $rebate->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now(),
                'rejected_by' => $rejectedBy ?? 'system'
            ]);

            // Update related product upload
            if ($rebate->productUpload) {
                $rebate->productUpload->update([
                    'rebate_status' => 'rejected'
                ]);
            }

            event(new RebateRejected($rebate));

            return [
                'success' => true,
                'message' => 'Rebate rejected successfully',
                'rebate' => $rebate
            ];

        } catch (\Exception $e) {
            Log::error('Rebate rejection error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create rebate record
     */
    protected function createRebateRecord(ProductUpload $productUpload, array $calculation): UserRebate
    {
        return UserRebate::create([
            'user_id' => $productUpload->user_id,
            'product_upload_id' => $productUpload->id,
            'rebate_program_id' => $calculation['program']->id,
            'type' => 'product_upload',
            'rebate_amount' => $calculation['rebate_amount'],
            'base_amount' => $calculation['base_amount'],
            'tier_multiplier' => $calculation['tier_multiplier'],
            'status' => 'pending',
            'calculation_details' => json_encode($calculation['calculation_details'] ?? []),
        ]);
    }

    /**
     * Credit user account
     */
    protected function creditUserAccount(User $user, UserRebate $rebate): void
    {
        // Update user balance
        $user->increment('balance', $rebate->rebate_amount);

        // Create transaction record
        $transaction = RebateTransaction::create([
            'user_id' => $user->id,
            'user_rebate_id' => $rebate->id,
            'type' => 'credit',
            'amount' => $rebate->rebate_amount,
            'status' => 'completed',
            'description' => "Rebate credit for " . ucwords(str_replace('_', ' ', $rebate->type)),
            'processed_at' => now()
        ]);

        // Create general transaction log
        $general = gs();
        $trx = getTrx();

        $transaction_log = new \App\Models\Transaction();
        $transaction_log->user_id = $user->id;
        $transaction_log->amount = $rebate->rebate_amount;
        $transaction_log->post_balance = $user->balance;
        $transaction_log->charge = 0;
        $transaction_log->trx_type = '+';
        $transaction_log->details = 'Rebate Credit';
        $transaction_log->trx = $trx;
        $transaction_log->remark = 'rebate_credit';
        $transaction_log->save();

        Log::info('User account credited', [
            'user_id' => $user->id,
            'rebate_id' => $rebate->id,
            'amount' => $rebate->rebate_amount,
            'new_balance' => $user->balance
        ]);
    }

    /**
     * Credit user account from rebate transaction
     */
    protected function creditUserAccountFromTransaction(User $user, RebateTransaction $transaction): void
    {
        // Update user balance
        $user->increment('balance', (float) $transaction->final_amount);

        // Update or create user rebate summary record
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

        $userRebate->addEarnings($transaction->final_amount, $transaction->description);

        // Create general transaction log
        $general = gs();
        $trx = getTrx();

        $transaction_log = new \App\Models\Transaction();
        $transaction_log->user_id = $user->id;
        $transaction_log->amount = $transaction->final_amount;
        $transaction_log->post_balance = $user->balance;
        $transaction_log->charge = 0;
        $transaction_log->trx_type = '+';
        $transaction_log->details = 'Rebate Credit - ' . $transaction->description;
        $transaction_log->trx = $trx;
        $transaction_log->remark = 'rebate_credit';
        $transaction_log->save();

        Log::info('User account credited from transaction', [
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'amount' => $transaction->final_amount,
            'new_balance' => $user->balance
        ]);
    }

    /**
     * Determine if rebate should be auto-approved
     */
    protected function shouldAutoApprove(ProductUpload $productUpload, array $calculation): bool
    {
        // Don't auto-approve if fraud score is high
        if (isset($calculation['calculation_details']['fraud_score']) && 
            $calculation['calculation_details']['fraud_score'] > 30) {
            return false;
        }

        // Don't auto-approve high amounts
        $general = gs();
        if ($calculation['rebate_amount'] > ($general->rebate_auto_approve_limit ?? 50)) {
            return false;
        }

        // Check user's success rate
        $user = $productUpload->user;
        $recentRebates = RebateTransaction::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        if ($recentRebates->count() > 5) {
            $rejectionRate = $recentRebates->where('status', 'rejected')->count() / $recentRebates->count();
            if ($rejectionRate > 0.3) { // More than 30% rejection rate
                return false;
            }
        }

        // Check if new user
        if ($user->created_at->diffInDays(now()) < 7) {
            return false; // New users require manual review
        }

        return true;
    }

    /**
     * Get review reason for manual review
     */
    protected function getReviewReason(array $calculation): string
    {
        $reasons = [];

        if (isset($calculation['calculation_details']['fraud_score']) && 
            $calculation['calculation_details']['fraud_score'] > 30) {
            $reasons[] = 'High fraud risk score';
        }

        if ($calculation['rebate_amount'] > 50) {
            $reasons[] = 'High rebate amount';
        }

        return implode(', ', $reasons) ?: 'Standard manual review';
    }

    /**
     * Log rebate rejection
     */
    protected function logRebateRejection(ProductUpload $productUpload, string $reason): void
    {
        Log::info('Rebate rejected', [
            'product_upload_id' => $productUpload->id,
            'user_id' => $productUpload->user_id,
            'reason' => $reason
        ]);
    }

    /**
     * Process loyalty rebates (monthly)
     */
    public function processLoyaltyRebates(): array
    {
        try {
            $processed = 0;
            $failed = 0;

            // Get eligible users (active users with sufficient activity)
            $eligibleUsers = User::active()
                ->whereHas('productUploads', function($query) {
                    $query->where('created_at', '>=', now()->subMonth());
                })
                ->get();

            foreach ($eligibleUsers as $user) {
                try {
                    $calculation = $this->calculatorService->calculateLoyaltyRebate($user);

                    if ($calculation['eligible']) {
                        $rebate = UserRebate::create([
                            'user_id' => $user->id,
                            'rebate_program_id' => $calculation['program']->id,
                            'type' => 'loyalty',
                            'rebate_amount' => $calculation['rebate_amount'],
                            'base_amount' => $calculation['base_amount'],
                            'tier_multiplier' => $calculation['tier_multiplier'],
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => 'system'
                        ]);

                        $this->creditUserAccount($user, $rebate);
                        $processed++;

                        event(new RebateApproved($rebate));
                    }
                } catch (\Exception $e) {
                    Log::error('Loyalty rebate processing failed for user: ' . $user->id, [
                        'error' => $e->getMessage()
                    ]);
                    $failed++;
                }
            }

            return [
                'success' => true,
                'processed' => $processed,
                'failed' => $failed,
                'eligible_users' => $eligibleUsers->count()
            ];

        } catch (\Exception $e) {
            Log::error('Loyalty rebate batch processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get processing statistics
     */
    public function getProcessingStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_processed' => RebateTransaction::where('created_at', '>=', $startDate)->count(),
            'approved' => RebateTransaction::where('created_at', '>=', $startDate)
                ->where('status', 'approved')->count(),
            'pending' => RebateTransaction::where('created_at', '>=', $startDate)
                ->where('status', 'pending')->count(),
            'rejected' => RebateTransaction::where('created_at', '>=', $startDate)
                ->where('status', 'rejected')->count(),
            'total_amount_paid' => RebateTransaction::where('created_at', '>=', $startDate)
                ->where('status', 'approved')
                ->sum('rebate_amount'),
            'average_processing_time' => $this->getAverageProcessingTime($days),
            'auto_approval_rate' => $this->getAutoApprovalRate($days)
        ];
    }

    /**
     * Get average processing time
     */
    protected function getAverageProcessingTime(int $days): float
    {
        $rebates = RebateTransaction::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('approved_at')
            ->select('created_at', 'approved_at')
            ->get();

        if ($rebates->isEmpty()) {
            return 0;
        }

        $totalMinutes = $rebates->sum(function($rebate) {
            return $rebate->created_at->diffInMinutes($rebate->approved_at);
        });

        return round($totalMinutes / $rebates->count(), 2);
    }

    /**
     * Get auto approval rate
     */
    protected function getAutoApprovalRate(int $days): float
    {
        $total = RebateTransaction::where('created_at', '>=', now()->subDays($days))
            ->where('status', 'approved')
            ->count();
        
        if ($total === 0) {
            return 0;
        }

        // Count auto-approved transactions using status changes table
        $autoApproved = DB::table('rebate_status_changes')
            ->join('rebate_transactions', 'rebate_status_changes.rebate_transaction_id', '=', 'rebate_transactions.id')
            ->where('rebate_transactions.created_at', '>=', now()->subDays($days))
            ->where('rebate_status_changes.new_status', 'approved')
            ->where('rebate_status_changes.changed_by', 'system')
            ->count();

        return round(($autoApproved / $total) * 100, 2);
    }

    /**
     * Bulk approve rebates
     */
    public function bulkApproveRebates(array $rebateIds, int $approvedBy): array
    {
        try {
            $approved = 0;
            $failed = 0;

            DB::beginTransaction();

            foreach ($rebateIds as $rebateId) {
                try {
                    $rebate = RebateTransaction::find($rebateId);
                    if ($rebate && $rebate->status === 'pending') {
                        $this->approveRebateTransaction($rebate, $approvedBy);
                        $approved++;
                    } else {
                        Log::warning("Rebate {$rebateId} not found or not pending", [
                            'exists' => $rebate ? true : false,
                            'status' => $rebate ? $rebate->status : null
                        ]);
                        $failed++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to approve rebate {$rebateId}: " . $e->getMessage());
                    $failed++;
                }
            }

            DB::commit();

            return [
                'success' => true,
                'approved' => $approved,
                'failed' => $failed
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk rebate approval error: ' . $e->getMessage());
            throw $e;
        }
    }
}