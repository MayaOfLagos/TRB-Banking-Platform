<?php

namespace App\Listeners;

use App\Events\RebateStatusChanged;
use App\Jobs\SendRebateNotification;
use Illuminate\Support\Facades\Log;

class HandleRebateStatusNotification
{
    /**
     * Handle the event.
     */
    public function handle(RebateStatusChanged $event)
    {
        $rebate = $event->rebate;
        $newStatus = $event->newStatus;
        $previousStatus = $event->previousStatus;

        Log::info('Rebate status changed', [
            'rebate_id' => $rebate->id,
            'user_id' => $rebate->user_id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus
        ]);

        // Determine notification type based on status change
        $notificationType = null;

        if ($newStatus === 'approved' && $previousStatus !== 'approved') {
            $notificationType = 'approved';
        } elseif ($newStatus === 'rejected' && $previousStatus !== 'rejected') {
            $notificationType = 'rejected';
        } elseif ($newStatus === 'pending' && $previousStatus === null) {
            // New submission
            $notificationType = 'submitted';
        }

        // Queue notification job if we have a valid type
        if ($notificationType) {
            SendRebateNotification::dispatch($rebate->id, $notificationType);
            
            Log::info('Queued rebate notification', [
                'rebate_id' => $rebate->id,
                'notification_type' => $notificationType
            ]);
        }
    }
}