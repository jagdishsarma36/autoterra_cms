<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Mail\OrderConfirmation;
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


protected static function booted(): void
{
    static::updated(function (Order $order) {
        $original = $order->getOriginal('status');
        $changed = $order->status;
        if ($original !== $changed && $order->user?->email) {
            Mail::to($order->user->email)->send(new OrderConfirmation($order, $original));
        }
    });
}
