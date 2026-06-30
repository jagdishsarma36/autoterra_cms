<?php

namespace App\Models;

use App\Mail\SubscriptionStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'term', 'razorpay_subscription_id',
        'razorpay_plan_id', 'status', 'current_period_start',
        'current_period_end', 'amount', 'currency',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'current_period_start' => 'datetime',
            'current_period_end' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Subscription $subscription) {
            if ($subscription->isDirty('status')) {
                try {
                    Mail::to($subscription->user->email)->send(
                        new SubscriptionStatusChanged(
                            $subscription,
                            $subscription->getOriginal('status'),
                            $subscription->status,
                        )
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send subscription status change email: ' . $e->getMessage());
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
