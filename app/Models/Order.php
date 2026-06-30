<?php

namespace App\Models;

use App\Mail\OrderStatusChanged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'term', 'currency', 'amount',
        'gst_amount', 'total_amount', 'razorpay_order_id', 'razorpay_payment_id',
        'status', 'billing_mode',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'gst_amount' => 'integer',
            'total_amount' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Order $order) {
            if ($order->isDirty('status')) {
                try {
                    Mail::to($order->user->email)->send(
                        new OrderStatusChanged(
                            $order,
                            $order->getOriginal('status'),
                            $order->status,
                        )
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send status change email: ' . $e->getMessage());
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
}
