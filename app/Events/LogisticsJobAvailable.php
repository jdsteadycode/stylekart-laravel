<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogisticsJobAvailable
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $job;      // The Order or the DeliveryJob
    public $type;     // 'order' or 'return'
    public $city;     // The city where the pickup is located
    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct($job, $type, $city, $order)
    {
        // log the action
        logger()->info("[app\Events\LogisticsJobAvailable@__construct] LogisticsJobAvailable is now fired.");

        $this->job = $job;
        $this->type = $type;
        $this->city = $city;
        $this->order = $order;
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
