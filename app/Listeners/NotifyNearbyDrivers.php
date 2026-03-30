<?php

// folder path
namespace App\Listeners;

// LogisticsJobAvailable event class path
use App\Events\LogisticsJobAvailable;

// User Model class path
use App\Models\User;

// Notification facade class path
use Illuminate\Support\Facades\Notification;

// Notification class path
use App\Notifications\Delivery\NewJobAvailableNotification; // Your existing one
use App\Notifications\Delivery\NewReturnJobAvailableNotification; // The new one

// Interface ShouldQueue path
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyNearbyDrivers implements ShouldQueue
{
    public function handle(LogisticsJobAvailable $event)
    {
        // log the action
        logger()->info("[app\Listeners\NotifyNearbyDrivers@handle] Handling LogisticsJobAvailable Event");

        // Find all delivery persons whose city is same as vendor's
        $drivers = User::where('role', 'delivery_person')
            ->whereHas('addresses', function ($query) use ($event) {
                $query->where('city', $event->city);
            })->get();

        // if no delivery persons found
        if ($drivers->isEmpty()) {

            // log the status
            logger()->info("No delivery persons found! Terminating the NotifyNearbyVendors.");

            return;
        }

        // check type?
        if ($event->type === 'order') {
            // Your existing notification (expects Order and City)
            Notification::send($drivers, new NewJobAvailableNotification($event->order, $event->city));
        }

        // if delivery is return?
        else {
            // The new notification for returns (expects DeliveryJob and City)
            Notification::send($drivers, new NewReturnJobAvailableNotification($event->job, $event->city));
        }
    }
}
