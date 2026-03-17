<?php

namespace App\Notifications\Delivery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewJobAvailableNotification extends Notification
{
    use Queueable;

    // variables
    protected $order;
    protected $city;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $city)
    {
        $this->order = $order;
        $this->city = $city;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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

    // store to database.
    public function toDatabase(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\NewJobAvailableNotification@toDatabase] Alerting Delivery Person ID: {$notifiable->id} about Order #{$this->order->id} in {$this->city}");

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'city' => $this->city,
            'message' => "New Job in {$this->city}! Order #{$this->order->order_number} is ready for pickup.",
            'action_url' => route('delivery.order.show', $this->order->id),
        ];
    }
}
