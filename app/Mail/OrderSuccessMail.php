<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    // order variable
    protected $order = null;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        // store order.
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You! Order Success: ' . $this->order->order_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Log that the mail content is being generated
        logger()->info("[app\Mail\OrderSuccessMail@content] Building invoice for Order #{$this->order->order_number}");

        return new Content(
            view: 'emails.order-success',
            with: [
                'order' => $this->order
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
