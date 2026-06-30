<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminSubscriptionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public string $type, // 'created' or 'status_changed'
        public ?string $oldStatus = null,
        public ?string $newStatus = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'created'
            ? 'New Subscription #' . $this->subscription->id . ' — ' . $this->subscription->product->name
            : 'Subscription #' . $this->subscription->id . ' Status Changed — ' . $this->subscription->product->name;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.admin-subscription-notification',
        );
    }
}
