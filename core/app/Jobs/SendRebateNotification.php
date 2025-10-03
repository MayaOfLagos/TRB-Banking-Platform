<?php

namespace App\Jobs;

use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRebateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rebateId;
    protected $notificationType;

    /**
     * Create a new job instance.
     */
    public function __construct($rebateId, $notificationType)
    {
        $this->rebateId = $rebateId;
        $this->notificationType = $notificationType;
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService)
    {
        try {
            $rebate = RebateTransaction::with(['user', 'program'])->find($this->rebateId);
            
            if (!$rebate) {
                Log::error('Rebate not found for notification', ['rebate_id' => $this->rebateId]);
                return;
            }

            switch ($this->notificationType) {
                case 'submitted':
                    $notificationService->sendRebateSubmitted($rebate);
                    break;
                    
                case 'approved':
                    $notificationService->sendRebateApproved($rebate);
                    // Check for tier advancement
                    $notificationService->checkTierAdvancement($rebate->user);
                    break;
                    
                case 'rejected':
                    $notificationService->sendRebateRejected($rebate);
                    break;
                    
                default:
                    Log::warning('Unknown notification type', [
                        'type' => $this->notificationType,
                        'rebate_id' => $this->rebateId
                    ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send rebate notification', [
                'rebate_id' => $this->rebateId,
                'type' => $this->notificationType,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Rebate notification job failed permanently', [
            'rebate_id' => $this->rebateId,
            'type' => $this->notificationType,
            'error' => $exception->getMessage()
        ]);
    }
}