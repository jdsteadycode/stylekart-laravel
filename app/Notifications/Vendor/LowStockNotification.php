<?php

namespace App\Notifications\Vendor;

use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    // variant value.
    protected $variant = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductVariant $variant)
    {
        $this->variant = $variant;
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
        // log the action
        logger()->info("[app\Notifications\Vendor\LowStockNotification@toMail] Mailing low stock intel");

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
        return [];
    }

    /**
     * save to db
     */
    public function toDatabase(object $notifiable): array
    {
        // log the action
        logger()->info("[app\Notifications\Vendor\LowStockNotification@toDatabase] Storing notifiying data in db");

        return [
            'product_name' => $this->variant->product->name,
            'current_stock' => $this->variant->stock,
            'message' => "Warning: {$this->variant->product->name} of (S:{$this->variant->size}, C: {$this->variant->color->name} ) is low on stock!",
        ];
    }
}
