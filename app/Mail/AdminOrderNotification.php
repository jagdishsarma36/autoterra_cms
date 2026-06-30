<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $type, // 'created' or 'status_changed'
        public ?string $oldStatus = null,
        public ?string $newStatus = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'created'
            ? 'New Order #' . $this->order->id . ' — ' . $this->order->product->name
            : 'Order #' . $this->order->id . ' Status Changed — ' . $this->order->product->name;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.admin-order-notification',
        );
    }
}
