<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'tier', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function getPriceForTerm(string $term, string $currency = 'INR'): ?int
    {
        $price = $this->prices()->where('term', $term)->where('is_active', true)->first();
        if (!$price) return null;

        return $currency === 'INR' ? $price->price_inr : $price->price_usd;
    }
}
