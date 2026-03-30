<?php

// folder path
namespace App\Notifications\Delivery;

// Traits
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

// Notification class path
use Illuminate\Notifications\Notification;

class ReturnJobAcceptedNotification extends Notification
{

    // job variable
    public $job;

    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($job)
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\ReturnJobAcceptedNotification@__construct] Notification instantiated.");

        // set the job data
        $this->job = $job;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\ReturnJobAcceptedNotification@via] Notification ways setup");

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\ReturnJobAcceptedNotification@toMail] Customer Mail Sending process initiated!");

        $driverName = $this->job->deliveryPerson->name;
        return (new MailMessage)
            ->subject('Rider Assigned for Your Return')
            ->line("Good news! Rider **{$driverName}** has accepted your return request.")
            ->line("They are heading to your location in **{$this->job->pickup_city}**.");
        // ->action('View Return Status', route('customer.returns.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Save the notification to database
     */
    public function toDatabase($notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\ReturnJobAcceptedNotification@toDatabase] Saved in Database, Notified the Customer via Notification (bell)");

        return [
            'message' => "Rider {$this->job->deliveryPerson->name} is coming to pick up your return!",
            'job_id' => $this->job->id,
        ];
    }
}
