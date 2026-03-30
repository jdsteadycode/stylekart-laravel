<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// OrderItem class path
use App\Models\OrderItem;

class RefundProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // order-item and amount variables.
    public $orderItem, $amount;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderItem $orderItem, $amount)
    {
        // log the action
        logger()->info("[app\Events\RefundProcessed@__construct] RefundProcessed Event is now fired!");

        // set the values..
        $this->orderItem = $orderItem;
        $this->amount = $amount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
