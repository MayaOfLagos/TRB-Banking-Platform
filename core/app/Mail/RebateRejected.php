<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserRebate;
use App\Models\RebateTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RebateRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $rebate;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(RebateTransaction $rebate, User $user)
    {
        $this->rebate = $rebate;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $general = gs();
        
        return $this->subject('Rebate Update - ' . $general->site_name)
                    ->markdown('templates.' . activeTemplateName() . '.mail.rebate_rejected')
                    ->with([
                        'rebate' => $this->rebate,
                        'user' => $this->user,
                        'general' => $general
                    ]);
    }
}