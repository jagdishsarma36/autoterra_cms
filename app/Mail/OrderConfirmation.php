<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation — ' . $this->order->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.order-confirmation',
        );
    }
}

public function __construct(
    public Order $order,
    public ?string $previousStatus = null,
) {}

public function envelope(): Envelope
{
    $subject = match ($this->order->status) {
        'paid' => 'Order Confirmed — ' . $this->order->product->name,
        'refunded' => 'Refund Processed — ' . $this->order->product->name,
        'cancelled' => 'Order Cancelled — ' . $this->order->product->name,
        'failed' => 'Payment Failed — ' . $this->order->product->name,
        default => 'Order Update — ' . $this->order->product->name,
    };
    return new Envelope(subject: $subject);
}
