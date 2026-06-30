<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Status Updated — ' . $this->order->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.order-status-changed',
        );
    }
}
