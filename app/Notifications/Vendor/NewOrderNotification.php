<?php

namespace App\Notifications\Vendor;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    // order variable
    protected $order = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\NewOrderNotification@via] Determining channels for order #{$this->order->id}");

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // log the action
        logger()->info("[app\Notifications\NewOrderNotification@toMail] Preparing email for {$notifiable->email}");

        return (new MailMessage)
            ->subject('New Order Received - Stylekart')
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('Great news! You have received a new order.')
            ->line('Order Number: '.$this->order->order_number)
            ->line('Total Amount: ₹'.number_format($this->order->total_price, 2))
            ->action('View Order Details', route('vendor.orders.index'))
            ->line('Please log in to your dashboard to process this order.');
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

    // save to database
    public function toDatabase(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\NewOrderNotification@toDatabase] Saving alert to database for Vendor ID: {$notifiable->id}");

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->user->name,
            'total_amount' => $this->order->total_price,
            'message' => 'New order #'.$this->order->id.' received from '.$this->order->user->name,
        ];
    }
}
