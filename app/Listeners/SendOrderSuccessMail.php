<?php

namespace App\Listeners;

// Interface for Queueing
use Illuminate\Contracts\Queue\ShouldQueue;

// class path to OrderPlacedEvent
use App\Events\OrderPlaced;

// get class path to OrderSuccessMailable
use App\Mail\OrderSuccessMail;

// get the Mail Class
use Illuminate\Support\Facades\Mail;

// Class UDE - SendOrderSuccessMail implementing ShouldQueue interface for queueing
class SendOrderSuccessMail implements ShouldQueue
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
        logger()->info("[app\Listeners\SendOrderSuccessMail@handle] SendOrderSuccessM   ail Listener handled OrderPlaced event");

        // get the order
        $order = $event->order;

        // get the associated customer who ordered!
        $customer = $order->user;

        // send the mail to customer
        Mail::to($customer->email)
            ->send(new OrderSuccessMail($order));

        // log the status
        logger()->info("Sent Invoice to Customer's mail-id: {$customer->email}");

        // log the end
        logger()->info("SendOrderSuccessMail sending email to customer finished.");
    }
}
