<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RebateSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $rebate;
    public $user;
    public $tierInfo;
    public $pendingAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(RebateTransaction $rebate, User $user, $tierInfo, $pendingAmount)
    {
        $this->rebate = $rebate;
        $this->user = $user;
        $this->tierInfo = $tierInfo;
        $this->pendingAmount = $pendingAmount;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $general = gs();
        
        return $this->subject('Rebate Submission Received - ' . $general->site_name)
                    ->markdown('templates.' . activeTemplateName() . '.mail.rebate_submitted')
                    ->with([
                        'rebate' => $this->rebate,
                        'user' => $this->user,
                        'tierInfo' => $this->tierInfo,
                        'pendingAmount' => $this->pendingAmount,
                        'general' => $general
                    ]);
    }
}