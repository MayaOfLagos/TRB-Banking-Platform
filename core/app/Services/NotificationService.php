<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use App\Mail\RebateApproved;
use App\Mail\RebateRejected;
use App\Mail\RebateSubmitted;
use App\Mail\TierAdvancement;
use App\Mail\Admin\AdminRebateApproved;
use App\Mail\Admin\AdminRebateRejected;
use App\Mail\Admin\AdminRebateSubmitted;
use App\Mail\Admin\AdminTierAdvancement;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send rebate approved notification
     */
    public function sendRebateApproved(RebateTransaction $rebate)
    {
        try {
            $user = $rebate->user;
            $tierInfo = $this->getUserTierInfo($user->id);
            $tierProgress = $this->calculateTierProgress($user, $tierInfo);

            // Send to user
            Mail::to($user->email)->send(new RebateApproved($rebate, $user, $tierInfo, $tierProgress));
            
            // Send to admins
            $this->sendAdminRebateApproved($rebate, $user, $tierInfo, $tierProgress);
            
            // Log notification
            Log::info('Rebate approved notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send rebate approved notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send rebate rejected notification
     */
    public function sendRebateRejected(RebateTransaction $rebate)
    {
        try {
            $user = $rebate->user;

            // Send to user
            Mail::to($user->email)->send(new RebateRejected($rebate, $user));
            
            // Send to admins
            $this->sendAdminRebateRejected($rebate, $user);
            
            Log::info('Rebate rejected notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send rebate rejected notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send rebate submitted notification
     */
    public function sendRebateSubmitted(RebateTransaction $rebate)
    {
        try {
            $user = $rebate->user;
            $tierInfo = $this->getUserTierInfo($user->id);
            
            // Calculate pending amount
            $pendingAmount = RebateTransaction::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('final_amount');

            // Send to user
            Mail::to($user->email)->send(new RebateSubmitted($rebate, $user, $tierInfo, $pendingAmount));
            
            // Send to admins
            $this->sendAdminRebateSubmitted($rebate, $user, $tierInfo, $pendingAmount);
            
            Log::info('Rebate submitted notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send rebate submitted notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send tier advancement notification
     */
    public function sendTierAdvancement(User $user, $previousTier, $newTier)
    {
        try {
            $tierMultipliers = [
                'Bronze' => 1.0,
                'Silver' => 1.25,
                'Gold' => 1.5,
                'Platinum' => 2.0
            ];

            $tierThresholds = [
                'Bronze' => 0,
                'Silver' => 1000,
                'Gold' => 5000,
                'Platinum' => 15000
            ];

            $previousMultiplier = $tierMultipliers[$previousTier];
            $newMultiplier = $tierMultipliers[$newTier];

            $totalEarned = UserRebate::where('user_id', $user->id)
                ->value('total_earned') ?? 0;

            $totalRebates = RebateTransaction::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();

            // Calculate next tier info
            $nextTier = $this->getNextTier($newTier);
            $amountToNext = 0;
            
            if ($nextTier) {
                $amountToNext = $tierThresholds[$nextTier] - $totalEarned;
            }

            // Send to user
            Mail::to($user->email)->send(new TierAdvancement(
                $user, 
                $previousTier, 
                $newTier, 
                $previousMultiplier, 
                $newMultiplier,
                $totalEarned,
                $totalRebates,
                $nextTier,
                $amountToNext
            ));
            
            // Send to admins
            $this->sendAdminTierAdvancement(
                $user, 
                $previousTier, 
                $newTier, 
                $previousMultiplier, 
                $newMultiplier,
                $totalEarned,
                $totalRebates,
                $nextTier,
                $amountToNext
            );
            
            Log::info('Tier advancement notification sent', [
                'user_id' => $user->id,
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send tier advancement notification', [
                'user_id' => $user->id,
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send SMS notification (basic implementation)
     */
    public function sendSMS($phone, $message)
    {
        try {
            // This would integrate with your SMS provider (Twilio, etc.)
            // For now, just log the SMS
            Log::info('SMS notification', [
                'phone' => $phone,
                'message' => $message
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification (basic implementation)
     */
    public function sendPushNotification($userId, $title, $body, $data = [])
    {
        try {
            // This would integrate with Firebase Cloud Messaging or similar
            // For now, store in database for API retrieval
            DB::table('push_notifications')->insert([
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'data' => json_encode($data),
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Push notification queued', [
                'user_id' => $userId,
                'title' => $title
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'user_id' => $userId,
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Check if user should be advanced to next tier
     */
    public function checkTierAdvancement(User $user)
    {
        $currentTierInfo = $this->getUserTierInfo($user->id);
        $currentTier = $currentTierInfo['tier'];

        $totalEarned = $currentTierInfo['total_earned'];

        $newTier = 'Bronze';
        if ($totalEarned >= 15000) {
            $newTier = 'Platinum';
        } elseif ($totalEarned >= 5000) {
            $newTier = 'Gold';
        } elseif ($totalEarned >= 1000) {
            $newTier = 'Silver';
        }

        // If tier has changed, send notification
        if ($newTier !== $currentTier) {
            $this->sendTierAdvancement($user, $currentTier, $newTier);
            
            // Also send SMS and push notification
            $this->sendSMS($user->mobile, "Congratulations! You've been promoted to {$newTier} tier!");
            $this->sendPushNotification(
                $user->id, 
                'Tier Advancement!', 
                "Congratulations! You're now a {$newTier} member!",
                ['type' => 'tier_advancement', 'new_tier' => $newTier]
            );

            return $newTier;
        }

        return false;
    }

    /**
     * Helper methods
     */
    private function getUserTierInfo($userId)
    {
        $totalEarned = UserRebate::where('user_id', $userId)
            ->value('total_earned') ?? 0;

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
            $multiplier = 1.25;
        }

        return [
            'tier' => $tier,
            'multiplier' => $multiplier,
            'total_earned' => $totalEarned,
        ];
    }

    private function calculateTierProgress($user, $tierInfo)
    {
        $totalEarned = UserRebate::where('user_id', $user->id)
            ->value('total_earned') ?? 0;

        $tierThresholds = [
            'Bronze' => 0,
            'Silver' => 1000,
            'Gold' => 5000,
            'Platinum' => 15000,
        ];

        $currentTier = $tierInfo['tier'] ?? 'Bronze';
        $nextTier = $this->getNextTier($currentTier);
        
        if (!$nextTier) {
            return null;
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

    /**
     * Get admin email addresses for notifications
     */
    private function getAdminEmails()
    {
        // Get from general settings or fallback to default admin emails
        $general = gs();
        
        // Check if admin notification emails are configured
        if (isset($general->admin_notification_emails) && !empty($general->admin_notification_emails)) {
            return explode(',', $general->admin_notification_emails);
        }
        
        // Fallback to default admin emails - you can configure these
        return [
            'admin@' . parse_url(config('app.url'), PHP_URL_HOST),
            'support@' . parse_url(config('app.url'), PHP_URL_HOST),
        ];
    }

    /**
     * Send admin notification for rebate submission
     */
    private function sendAdminRebateSubmitted(RebateTransaction $rebate, User $user, $tierInfo, $pendingAmount)
    {
        try {
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new AdminRebateSubmitted($rebate, $user, $tierInfo, $pendingAmount));
                }
            }
            
            Log::info('Admin rebate submitted notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'admin_emails' => $adminEmails
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin rebate submitted notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send admin notification for rebate approval
     */
    private function sendAdminRebateApproved(RebateTransaction $rebate, User $user, $tierInfo, $tierProgress)
    {
        try {
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new AdminRebateApproved($rebate, $user, $tierInfo, $tierProgress));
                }
            }
            
            Log::info('Admin rebate approved notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'admin_emails' => $adminEmails
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin rebate approved notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send admin notification for rebate rejection
     */
    private function sendAdminRebateRejected(RebateTransaction $rebate, User $user)
    {
        try {
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new AdminRebateRejected($rebate, $user));
                }
            }
            
            Log::info('Admin rebate rejected notification sent', [
                'user_id' => $user->id,
                'rebate_id' => $rebate->id,
                'admin_emails' => $adminEmails
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin rebate rejected notification', [
                'user_id' => $rebate->user_id,
                'rebate_id' => $rebate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send admin notification for tier advancement
     */
    private function sendAdminTierAdvancement(
        User $user, 
        $previousTier, 
        $newTier, 
        $previousMultiplier, 
        $newMultiplier,
        $totalEarned,
        $totalRebates,
        $nextTier = null,
        $amountToNext = 0
    ) {
        try {
            $adminEmails = $this->getAdminEmails();
            
            foreach ($adminEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->send(new AdminTierAdvancement(
                        $user, 
                        $previousTier, 
                        $newTier, 
                        $previousMultiplier, 
                        $newMultiplier,
                        $totalEarned,
                        $totalRebates,
                        $nextTier,
                        $amountToNext
                    ));
                }
            }
            
            Log::info('Admin tier advancement notification sent', [
                'user_id' => $user->id,
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'admin_emails' => $adminEmails
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send admin tier advancement notification', [
                'user_id' => $user->id,
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}