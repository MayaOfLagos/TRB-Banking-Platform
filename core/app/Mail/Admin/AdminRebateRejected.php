<?php

namespace App\Mail\Admin;

use App\Models\User;
use App\Models\RebateTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminRebateRejected extends Mailable
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
        
        return $this->subject('Rebate Rejected - User Notified - ' . $general->site_name)
                    ->markdown('templates.' . activeTemplateName() . '.mail.admin.rebate_rejected')
                    ->with([
                        'rebate' => $this->rebate,
                        'user' => $this->user,
                        'general' => $general
                    ]);
    }
}