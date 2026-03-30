<?php

// folder path
namespace App\Listeners;

// get RefundProcessed Event class path
use App\Events\RefundProcessed;

// get RefundProcessedNotification class path
use App\Notifications\Delivery\RefundProcessedNotification;

// Interface ShouldQueue path
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

// Class UDE - SendRefundNotification implementing ShouldQueue interface for queueing
class SendRefundNotification implements ShouldQueue
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
    public function handle(RefundProcessed $event): void
    {
        // log the action
        logger()->info("[app\Listeners\SendRefundNotification@handle] SendRefundNotification Listener handled RefundProcessed event");

        // Fetch the user from the order item
        $customer = $event->orderItem->order->user;

        // Send the notification we created earlier
        $customer->notify(new RefundProcessedNotification(
            amount: $event->amount,
            orderNumber: $event->orderItem->order->order_number
        ));

        // log the status
        logger()->info("Sent Mail to {$customer->name}!", [
            // Pull from the orderItem directly, not the customer
            "order-number" => $event->orderItem->order?->order_number ?? 'N/A',
            "product"      => $event->orderItem->product?->name ?? 'N/A',
        ]);

        // log the end
        logger()->info("Listener execution end.");
    }
}
