<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    public function getItems(): array
    {
        return Session::get(self::SESSION_KEY . '.items', []);
    }

    public function getCouponCode(): ?string
    {
        return Session::get(self::SESSION_KEY . '.coupon_code');
    }

    public function addItem(string $productSlug, string $term, int $quantity = 1): void
    {
        $items = $this->getItems();
        $key = $productSlug . '|' . $term;

        if (isset($items[$key])) {
            $items[$key]['quantity'] += $quantity;
        } else {
            $items[$key] = [
                'product_slug' => $productSlug,
                'term' => $term,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::SESSION_KEY . '.items', $items);
    }

    public function removeItem(string $productSlug, string $term): void
    {
        $items = $this->getItems();
        $key = $productSlug . '|' . $term;
        unset($items[$key]);
        Session::put(self::SESSION_KEY . '.items', $items);
    }

    public function updateQuantity(string $productSlug, string $term, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productSlug, $term);
            return;
        }

        $items = $this->getItems();
        $key = $productSlug . '|' . $term;

        if (isset($items[$key])) {
            $items[$key]['quantity'] = $quantity;
            Session::put(self::SESSION_KEY . '.items', $items);
        }
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getItemCount(): int
    {
        return array_sum(array_column($this->getItems(), 'quantity'));
    }

    /**
     * Compute subtotal in rupees from product prices.
     */
    public function getSubtotal(string $currency = 'INR'): float
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $price = $this->getItemPrice($item['product_slug'], $item['term'], $currency);
            if ($price !== null) {
                $subtotal += $price * $item['quantity'];
            }
        }
        return $subtotal;
    }

    /**
     * Get price for a single item in rupees (INR) or dollars (USD).
     */
    public function getItemPrice(string $productSlug, string $term, string $currency = 'INR'): ?float
    {
        $product = Product::where('slug', $productSlug)->first();
        if (!$product) return null;

        $price = $product->getPriceForTerm($term, $currency);
        if ($price === null) return null;

        // product_prices stores INR in rupees, USD in dollars
        return (float) $price;
    }

    /**
     * Get detailed cart contents with computed prices.
     */
    public function getContents(string $currency = 'INR'): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $product = Product::where('slug', $item['product_slug'])->first();
            $price = $this->getItemPrice($item['product_slug'], $item['term'], $currency);

            if ($product && $price !== null) {
                $gst = $currency === 'INR' ? round($price * 0.18) : 0;
                $items[] = [
                    'product_slug' => $item['product_slug'],
                    'product_name' => $product->name,
                    'term' => $item['term'],
                    'term_label' => termLabel($item['term']),
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'gst' => $gst,
                    'line_total' => ($price + $gst) * $item['quantity'],
                ];
            }
        }

        $subtotal = array_sum(array_column($items, 'unit_price')) > 0
            ? array_sum(array_map(fn($i) => $i['unit_price'] * $i['quantity'], $items))
            : 0;
        $totalGst = array_sum(array_column($items, 'gst')) > 0
            ? array_sum(array_map(fn($i) => $i['gst'] * $i['quantity'], $items))
            : 0;
        $discount = $this->getDiscount($currency);
        $total = $subtotal + $totalGst - $discount;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'gst' => $totalGst,
            'discount' => $discount,
            'total' => max(0, $total),
            'coupon_code' => $this->getCouponCode(),
        ];
    }

    /**
     * Apply a coupon code. Returns true on success, error string on failure.
     */
    public function applyCoupon(string $code): bool|string
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();
        if (!$coupon) {
            return 'Invalid coupon code.';
        }

        $subtotal = $this->getSubtotal('INR');
        $productSlugs = array_column($this->getItems(), 'product_slug');
        $result = $coupon->validateForCart($subtotal, $productSlugs);

        if (!$result['valid']) {
            return $result['error'];
        }

        Session::put(self::SESSION_KEY . '.coupon_code', strtoupper(trim($code)));
        return true;
    }

    public function removeCoupon(): void
    {
        Session::forget(self::SESSION_KEY . '.coupon_code');
    }

    /**
     * Get discount amount in rupees.
     */
    public function getDiscount(string $currency = 'INR'): float
    {
        $code = $this->getCouponCode();
        if (!$code) return 0;

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return 0;

        $subtotal = $this->getSubtotal($currency);
        return $coupon->calculateDiscount($subtotal);
    }

    /**
     * Get total in rupees (subtotal + GST - discount).
     */
    public function getTotal(string $currency = 'INR'): float
    {
        $contents = $this->getContents($currency);
        return $contents['total'];
    }

    /**
     * Convert rupees to paise for Razorpay.
     */
    public function totalToPaise(): int
    {
        return toPaise($this->getTotal('INR'));
    }

    /**
     * Get all product slugs in the cart.
     */
    public function getProductSlugs(): array
    {
        return array_column($this->getItems(), 'product_slug');
    }
}
