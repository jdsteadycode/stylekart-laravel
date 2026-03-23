<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

// get OrderPlacedEvent class path.
use App\Events\OrderPlaced;

// get Log class path
use Illuminate\Support\Facades\Log;

class LogOrderPlaced
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        // log the action
        Log::info("[app\Listeners\LogOrderPlaced@handle] Logging Order Placed Event!");

        // log the event
        Log::info("Order Placed: " . $event->order?->order_number);
    }
}
