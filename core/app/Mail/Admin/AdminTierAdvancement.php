<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminTierAdvancement extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $previousTier;
    public $newTier;
    public $previousMultiplier;
    public $newMultiplier;
    public $totalEarned;
    public $totalRebates;
    public $nextTier;
    public $amountToNext;

    /**
     * Create a new message instance.
     */
    public function __construct(
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
        $this->user = $user;
        $this->previousTier = $previousTier;
        $this->newTier = $newTier;
        $this->previousMultiplier = $previousMultiplier;
        $this->newMultiplier = $newMultiplier;
        $this->totalEarned = $totalEarned;
        $this->totalRebates = $totalRebates;
        $this->nextTier = $nextTier;
        $this->amountToNext = $amountToNext;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $general = gs();
        
        return $this->subject('User Tier Advancement - ' . $this->newTier . ' Achieved - ' . $general->site_name)
                    ->markdown('templates.' . activeTemplateName() . '.mail.admin.tier_advancement')
                    ->with([
                        'user' => $this->user,
                        'previousTier' => $this->previousTier,
                        'newTier' => $this->newTier,
                        'previousMultiplier' => $this->previousMultiplier,
                        'newMultiplier' => $this->newMultiplier,
                        'totalEarned' => $this->totalEarned,
                        'totalRebates' => $this->totalRebates,
                        'nextTier' => $this->nextTier,
                        'amountToNext' => $this->amountToNext,
                        'general' => $general
                    ]);
    }
}