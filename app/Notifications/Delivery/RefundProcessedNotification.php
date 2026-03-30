<?php

namespace App\Notifications\Delivery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundProcessedNotification extends Notification
{
    use Queueable;

    // values..
    public $orderNumber, $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct($amount, $orderNumber)
    {
        // set the values..
        $this->amount = $amount;
        $this->orderNumber = $orderNumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\RefundProcessedNotification@via] Setting ways to Notify");

        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // log the action
        logger()->info("[app\Notifications\Delivery\RefundProcessedNotification@toMail] Mail Notificaiton iniatiated");

        // send the mail
        return (new MailMessage)
            ->subject('Refund Processed - Stylekart Wallet')
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('Your return for Order #' . $this->orderNumber . ' has been successfully received and processed.')
            ->line('We have credited **₹' . number_format($this->amount, 2) . '** to your Stylekart Wallet.')
            ->action('View My Wallet', route('customer.wallet.index'))
            ->line('Thank you for shopping with Stylekart!');
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
}
