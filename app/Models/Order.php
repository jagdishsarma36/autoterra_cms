<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
