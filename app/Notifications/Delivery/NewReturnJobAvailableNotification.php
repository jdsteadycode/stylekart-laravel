<?php

// folder path
namespace App\Notifications\Delivery;

use Illuminate\Bus\Queueable;

// Notification class path
use Illuminate\Notifications\Notification;

// DeliveryJob model class path
use App\Models\DeliveryJob;


class NewReturnJobAvailableNotification extends Notification
{
    use Queueable;

    // job and city
    protected $job;
    protected $city;

    public function __construct(DeliveryJob $job, $city)
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\NewReturnJobAvailableNotification@__construct] Setting up the values");

        // set the values
        $this->job = $job;
        $this->city = $city;
    }

    public function via(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\NewReturnJobAvailableNotification@via] Notifying the delivery persons");

        return ['database']; // Keep it consistent with your order notification
    }

    public function toDatabase(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\NewReturnJobAvailableNotification@toDatabase] Alerting Driver ID: {$notifiable->id} about Return Job #{$this->job->id} in {$this->city}");

        return [
            'job_id' => $this->job->id,
            'type' => 'return',
            'city' => $this->city,
            'message' => "New Return Pickup in {$this->city}! Job #{$this->job->id} is ready for collection.",
            'action_url' => route('delivery.return.show', $this->job->id), // Send them to the returns tab
        ];
    }
}
