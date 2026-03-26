<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowVariantStockReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // variant variable..
    public $variant;

    /**
     * Create a new event instance.
     */
    public function __construct($variant)
    {
        // log the action
        logger()->info("[app\Events\LowVariantStockReached@__construct] LowVariantStockReached Event is now fired!");

        // set the variant incoming from event.
        $this->variant = $variant;
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
