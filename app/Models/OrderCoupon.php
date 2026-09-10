<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCoupon extends Model
{
    protected $fillable = ['order_id', 'coupon_id', 'discount'];

    protected function casts(): array
    {
        return [
            'discount' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
