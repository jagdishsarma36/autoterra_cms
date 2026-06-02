<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $fillable = ['product_id', 'term', 'price_inr', 'price_usd', 'is_active', 'billing_cycles'];

    protected function casts(): array
    {
        return [
            'price_inr' => 'integer',
            'price_usd' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
