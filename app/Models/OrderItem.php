<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'term', 'quantity',
        'unit_price_inr', 'unit_price_usd', 'gst_amount', 'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price_inr' => 'integer',
            'unit_price_usd' => 'integer',
            'gst_amount' => 'integer',
            'total_amount' => 'integer',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
