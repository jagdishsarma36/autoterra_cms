<?php

namespace App\Models;

use App\Mail\AdminOrderNotification;
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
        static::created(function (Order $order) {
            try {
                $adminEmail = Setting::get('site_email', 'support@autoterra.net');
                Mail::to($adminEmail)->send(new AdminOrderNotification($order, 'created'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send admin order notification: ' . $e->getMessage());
            }
        });

        static::updating(function (Order $order) {
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;
                try {
                    Mail::to($order->user->email)->send(
                        new OrderStatusChanged($order, $oldStatus, $newStatus)
                    );
                    $adminEmail = Setting::get('site_email', 'support@autoterra.net');
                    Mail::to($adminEmail)->send(
                        new AdminOrderNotification($order, 'status_changed', $oldStatus, $newStatus)
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
