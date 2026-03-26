<?php

namespace App\Listeners;

// get the LowVariantStockReached Event
use App\Events\LowVariantStockReached;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowVariantStockNotification
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
    public function handle(LowVariantStockReached $event): void
    {

        // log the action..
        logger()->info("[app\Listeners\SendLowVariantStockNotification@handle] Notifying Vendors, handled the LowVariantStockReached Event");

        // get the variant
        $variant = $event->variant;

        // get the vendor
        $vendor = $variant->product->vendor;

        // Notify the vendor immediately about this specific variant
        $vendor->notify(new \App\Notifications\Vendor\LowStockNotification($variant));

        // log the warning.
        logger()->warning("Low stock alert! Variant: {$variant->id} is at {$variant->stock}");

        // log the end
        logger()->info("Low variant stock notifying ended");
    }
}
