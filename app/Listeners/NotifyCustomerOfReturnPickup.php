<?php

namespace App\Listeners;

// ReturnJobAccepted Event class path
use App\Events\ReturnJobAccepted;

// Traits path
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

// OrderItem class path
use App\Models\OrderItem;

// ReturnJobAccepted Notification
use App\Notifications\Delivery\ReturnJobAcceptedNotification;

class NotifyCustomerOfReturnPickup
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
    public function handle(ReturnJobAccepted $event): void
    {
        // log the action
        logger()->info("[app\Listeners\NotifyCustomerOfReturnPickup@handle] Handling the ReturnJobAccepted Event");

        // Find the original item to get the customer
        $orderItem = OrderItem::find($event->job->reference_id);

        // if orderItem or it's related order doesn't exist then!
        if (!$orderItem || !$orderItem->order) {
            logger()->error("Failed to find Order relationship for Return Job #{$event->job->id}");
            return;
        }

        // get the customer who ordered the item!
        $customer = $orderItem->order->user;

        // Fire your fixed notification!
        $customer->notify(new ReturnJobAcceptedNotification($event->job));

        // log the status
        logger()->info("Customer {$customer->name} notified of rider acceptance.");

        // log the end
        logger()->info("Listener process ended!");
    }
}
