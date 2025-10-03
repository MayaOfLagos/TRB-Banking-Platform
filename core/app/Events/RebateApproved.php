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

class RebateApproved implements ShouldBroadcast
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
            new Channel('rebate.approved')
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'rebate.approved';
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
            'approved_at' => $this->rebate->approved_at->toISOString(),
            'message' => 'Your rebate of $' . number_format($this->rebate->rebate_amount, 2) . ' has been approved!'
        ];
    }
}
