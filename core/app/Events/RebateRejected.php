<?php

namespace App\Events;

use App\Models\UserRebate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RebateRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rebate;

    /**
     * Create a new event instance.
     */
    public function __construct(UserRebate $rebate)
    {
        $this->rebate = $rebate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('rebate.' . $this->rebate->user_id),
            new Channel('rebate.rejected')
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rebate.rejected';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'rebate_id' => $this->rebate->id,
            'user_id' => $this->rebate->user_id,
            'amount' => $this->rebate->rebate_amount,
            'type' => $this->rebate->type,
            'rejection_reason' => $this->rebate->rejection_reason,
            'message' => 'Your rebate submission has been rejected. Reason: ' . $this->rebate->rejection_reason
        ];
    }
}
