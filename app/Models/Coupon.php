<?php

namespace App\Models;

use App\Models\Casts\AsProductIds;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'description', 'type', 'value',
        'min_order_amount', 'max_uses', 'used_count',
        'product_ids', 'is_active', 'starts_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'min_order_amount' => 'integer',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'product_ids' => AsProductIds::class,
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function orderCoupons()
    {
        return $this->hasMany(OrderCoupon::class);
    }

    /**
     * Validate coupon for a given cart subtotal (in rupees) and product slugs.
     * Returns ['valid' => true, 'discount' => int] on success (discount in rupees),
     * or ['valid' => false, 'error' => string] on failure.
     */
    public function validateForCart(float $subtotalRupees, array $productSlugs): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'error' => 'This coupon is no longer active.'];
        }

        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return ['valid' => false, 'error' => 'This coupon is not yet valid.'];
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return ['valid' => false, 'error' => 'This coupon has expired.'];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'error' => 'This coupon has reached its usage limit.'];
        }

        if ($this->min_order_amount !== null) {
            $minRupees = $this->min_order_amount / 100;
            if ($subtotalRupees < $minRupees) {
                return ['valid' => false, 'error' => 'Minimum order amount of ₹' . number_format($minRupees, 0) . ' required.'];
            }
        }

        if (!empty($this->product_ids)) {
            $applicableProducts = Product::whereIn('id', $this->product_ids)->pluck('slug')->toArray();
            $overlapping = array_intersect($productSlugs, $applicableProducts);
            if (empty($overlapping)) {
                return ['valid' => false, 'error' => 'This coupon is not applicable to the products in your cart.'];
            }
        }

        $discount = $this->calculateDiscount($subtotalRupees);

        return ['valid' => true, 'discount' => $discount];
    }

    /**
     * Calculate discount amount in rupees from a subtotal in rupees.
     */
    public function calculateDiscount(float $subtotalRupees): float
    {
        if ($this->type === 'percentage') {
            return round($subtotalRupees * ($this->value / 100), 2);
        }

        // Fixed discount in paise, convert to rupees
        return $this->value / 100;
    }

    /**
     * Increment the usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
