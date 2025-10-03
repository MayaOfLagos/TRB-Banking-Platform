<?php

namespace App\Events;

use App\Models\UserRebate;
use App\Models\RebateTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RebateStatusChanged
{
    use Dispatchable, SerializesModels;

    public $rebate;
    public $previousStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(RebateTransaction $rebate, $previousStatus, $newStatus)
    {
        $this->rebate = $rebate;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
    }
}