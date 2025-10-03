<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RebateApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $rebate;
    public $user;
    public $tierInfo;
    public $tierProgress;

    /**
     * Create a new message instance.
     */
    public function __construct(RebateTransaction $rebate, User $user, $tierInfo, $tierProgress = null)
    {
        $this->rebate = $rebate;
        $this->user = $user;
        $this->tierInfo = $tierInfo;
        $this->tierProgress = $tierProgress;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $general = gs();
        
        return $this->subject('Rebate Approved - ' . $general->site_name)
                    ->markdown('templates.' . activeTemplateName() . '.mail.rebate_approved')
                    ->with([
                        'rebate' => $this->rebate,
                        'user' => $this->user,
                        'tierInfo' => $this->tierInfo,
                        'tierProgress' => $this->tierProgress,
                        'general' => $general
                    ]);
    }
}