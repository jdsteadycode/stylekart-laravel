<?php

namespace App\Listeners;

// path to interface ShouldQueue
use Illuminate\Contracts\Queue\ShouldQueue;

// get OrderPlacedEvent class path
use App\Events\OrderPlaced;

// get User Model Class path
use App\Models\User;

// get NewOrderNotification Class path
use App\Notifications\Vendor\NewOrderNotification;

// Class UDE - NotifyVendors implementing ShouldQueue interface for queueing
class NotifyVendors implements ShouldQueue
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
        // log the action..
        logger()->info("[app\Listeners\NotifyVendors@handle] Notifying Vendors Listener handled the OrderPlaced Event");

        // get the order..
        $order = $event->order;

        // get vendords associated to this order!
        $vendorIds = $order->items->pluck('vendor_id')->unique();

        // for each vendor
        foreach($vendorIds as $vendorId) {

            // get the vendor
            $vendor = User::find($vendorId);

            // if vendor exists?
            if($vendor) {

                // notify the vendor
                $vendor->notify(new NewOrderNotification($order));

                // log the status
                logger()->info("Order notification sent to Vendor: {$vendor->name} (ID: {$vendorId})");
            }
        }

        // log the end
        logger()->info("Vendor(s) notified | Notify vendors end.");
    }
}
