<?php

namespace App\Listeners;

// path to interface ShouldQueue
use Illuminate\Contracts\Queue\ShouldQueue;

// get OrderPlacedEvent class path.
use App\Events\OrderPlaced;

// get Log class path
use Illuminate\Support\Facades\Log;

// Class UDE - LogOrderPlaced implementing ShouldQueue interface for queueing
class LogOrderPlaced implements ShouldQueue
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
