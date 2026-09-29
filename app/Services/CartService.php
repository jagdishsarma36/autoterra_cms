<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** One-time payment for the whole term up front. */
    public const MODE_UPFRONT = 'upfront';

    /** Recurring Razorpay subscription (one plan per cart line). */
    public const MODE_SUBSCRIPTION = 'monthly';

    public const GST_RATE = 0.18;

    /**
     * Billing cycle for each term. `cycles` is how many times the plan is
     * charged over the whole term, so the per-cycle amount can be derived by
     * dividing the term price by it. Daily/weekly prices are already
     * per-cycle, so they are never divided.
     */
    public static function billingCycle(string $term): array
    {
        return match ($term) {
            'daily' => ['period' => 'daily', 'interval' => 1, 'cycles' => 30, 'per_cycle_price' => true],
            'weekly' => ['period' => 'weekly', 'interval' => 1, 'cycles' => 4, 'per_cycle_price' => true],
            '3mo' => ['period' => 'monthly', 'interval' => 3, 'cycles' => 1, 'per_cycle_price' => false],
            '6mo' => ['period' => 'monthly', 'interval' => 6, 'cycles' => 1, 'per_cycle_price' => false],
            '1yr' => ['period' => 'monthly', 'interval' => 1, 'cycles' => 12, 'per_cycle_price' => false],
            '3yr' => ['period' => 'yearly', 'interval' => 1, 'cycles' => 3, 'per_cycle_price' => false],
            '5yr' => ['period' => 'yearly', 'interval' => 1, 'cycles' => 5, 'per_cycle_price' => false],
            default => ['period' => 'monthly', 'interval' => 1, 'cycles' => 12, 'per_cycle_price' => false],
        };
    }

    public static function normalizeBillingMode(?string $mode): string
    {
        return $mode === self::MODE_SUBSCRIPTION ? self::MODE_SUBSCRIPTION : self::MODE_UPFRONT;
    }

    public function getItems(): array
    {
        $stored = Session::get(self::SESSION_KEY . '.items', []);
        $items = [];
        $migrated = false;

        foreach ($stored as $key => $item) {
            // Carts stored before billing modes existed used "slug|term" keys
            // and have no billing mode; they are one-time purchases.
            $mode = self::normalizeBillingMode($item['billing_mode'] ?? null);
            $newKey = $this->itemKey($item['product_slug'], $item['term'], $mode);

            if ($newKey !== $key) {
                $migrated = true;
            }

            $item['billing_mode'] = $mode;
            $item['quantity'] = $mode === self::MODE_SUBSCRIPTION ? 1 : max(1, (int) $item['quantity']);

            // A migration collision means both lines are the same purchase.
            $items[$newKey] = isset($items[$newKey])
                ? $items[$newKey]
                : $item;
        }

        if ($migrated) {
            Session::put(self::SESSION_KEY . '.items', $items);
        }

        return $items;
    }

    public function getCouponCode(): ?string
    {
        return Session::get(self::SESSION_KEY . '.coupon_code');
    }

    public function addItem(string $productSlug, string $term, int $quantity = 1, string $billingMode = self::MODE_UPFRONT): void
    {
        $items = $this->getItems();
        $billingMode = self::normalizeBillingMode($billingMode);
        $key = $this->itemKey($productSlug, $term, $billingMode);

        // Subscriptions are per-seat plans (Razorpay quantity 1), so a
        // subscription line can never hold more than one seat — re-adding the
        // same subscription product just keeps the existing line.
        $isSub = $billingMode === self::MODE_SUBSCRIPTION;
        $quantity = $isSub ? 1 : max(1, $quantity);

        if (isset($items[$key])) {
            $items[$key]['quantity'] = $isSub ? 1 : $items[$key]['quantity'] + $quantity;
            $items[$key]['billing_mode'] = $billingMode;
        } else {
            $items[$key] = [
                'product_slug' => $productSlug,
                'term' => $term,
                'quantity' => $quantity,
                'billing_mode' => $billingMode,
            ];
        }

        Session::put(self::SESSION_KEY . '.items', $items);

        // An applied coupon is kept. It discounts the one-time lines only, and
        // a subscription cannot inflate that amount, so adding one does not
        // invalidate it. The cart UI notes the split when it matters.
    }

    /**
     * Cart line key. Billing mode is part of the key so the same product can
     * be queued as a one-time purchase and as a subscription at the same time.
     */
    public static function itemKey(string $productSlug, string $term, ?string $billingMode = null): string
    {
        return $productSlug . '|' . $term . '|' . self::normalizeBillingMode($billingMode);
    }

    public function removeItem(string $productSlug, string $term, ?string $billingMode = null): void
    {
        $items = $this->getItems();
        $modes = $billingMode !== null
            ? [self::normalizeBillingMode($billingMode)]
            : [self::MODE_UPFRONT, self::MODE_SUBSCRIPTION];

        foreach ($modes as $mode) {
            unset($items[$this->itemKey($productSlug, $term, $mode)]);
        }

        Session::put(self::SESSION_KEY . '.items', $items);
    }

    public function updateQuantity(string $productSlug, string $term, int $quantity, ?string $billingMode = null): void
    {
        if ($quantity <= 0) {
            $this->removeItem($productSlug, $term, $billingMode);
            return;
        }

        $items = $this->getItems();

        $modes = $billingMode !== null
            ? [self::normalizeBillingMode($billingMode)]
            : [self::MODE_UPFRONT, self::MODE_SUBSCRIPTION];

        foreach ($modes as $mode) {
            $key = $this->itemKey($productSlug, $term, $mode);
            if (!isset($items[$key])) {
                continue;
            }
            $items[$key]['quantity'] = $mode === self::MODE_SUBSCRIPTION ? 1 : $quantity;
        }

        Session::put(self::SESSION_KEY . '.items', $items);
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
     * Compute the amount charged today, excluding GST.
     *
     * A one-time line is charged its whole term price; a subscription line is
     * charged one billing cycle, with the remainder recurring later.
     */
    public function getSubtotal(string $currency = 'INR'): float
    {
        return $this->sumSubtotal($currency, false) + $this->sumSubtotal($currency, true);
    }

    /**
     * Subtotal of just the one-time lines. This is the only money a coupon can
     * reduce: a subscription is billed on a Razorpay plan that charges one
     * fixed amount on every cycle, so there is no per-payment figure to
     * discount.
     */
    public function getUpfrontSubtotal(string $currency = 'INR'): float
    {
        return $this->sumSubtotal($currency, false);
    }

    protected function sumSubtotal(string $currency, bool $subscriptions): float
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $price = $this->getItemPrice($item['product_slug'], $item['term'], $currency);
            if ($price === null) continue;

            $isSub = self::normalizeBillingMode($item['billing_mode'] ?? null) === self::MODE_SUBSCRIPTION;
            if ($isSub !== $subscriptions) continue;

            $subtotal += $isSub
                ? self::getCycleBase($price, $item['term'])
                : $price * $item['quantity'];
        }
        return $subtotal;
    }

    /**
     * Slugs of the one-time lines, i.e. the products a coupon can apply to.
     *
     * @return array<int, string>
     */
    public function getUpfrontProductSlugs(): array
    {
        $slugs = [];
        foreach ($this->getItems() as $item) {
            if (self::normalizeBillingMode($item['billing_mode'] ?? null) !== self::MODE_SUBSCRIPTION) {
                $slugs[] = $item['product_slug'];
            }
        }
        return array_values(array_unique($slugs));
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
     *
     * Every total reflects what the customer is charged right now, so a
     * subscription line contributes one cycle rather than its whole term.
     */
    public function getContents(string $currency = 'INR'): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $product = Product::where('slug', $item['product_slug'])->first();
            $price = $this->getItemPrice($item['product_slug'], $item['term'], $currency);

            if ($product && $price !== null) {
                $billingMode = self::normalizeBillingMode($item['billing_mode'] ?? null);
                $isSub = $billingMode === self::MODE_SUBSCRIPTION;
                $quantity = $isSub ? 1 : $item['quantity'];

                $unit = $isSub ? $this->getCycleBase($price, $item['term']) : $price;
                $gst = $currency === 'INR' ? round($unit * self::GST_RATE) : 0;
                $termGst = $currency === 'INR' ? round($price * self::GST_RATE) : 0;

                $items[] = [
                    'product_slug' => $item['product_slug'],
                    'product_name' => $product->name,
                    'term' => $item['term'],
                    'term_label' => termLabel($item['term']),
                    'quantity' => $quantity,
                    'billing_mode' => $billingMode,
                    'is_subscription' => $isSub,
                    'unit_price' => $price,
                    'gst' => $gst,
                    'line_total' => ($unit + $gst) * $quantity,
                    'term_total' => ($price + $termGst) * $quantity,
                    'cycle_base' => $isSub ? round($unit, 2) : null,
                    'cycle_amount' => $isSub ? $this->getCycleAmount($price, $item['term'], $currency) : null,
                    'billing_cycles' => self::billingCycle($item['term'])['cycles'],
                ];
            }
        }

        $subtotal = array_sum(array_map(
            fn($i) => ($i['cycle_base'] ?? $i['unit_price']) * $i['quantity'],
            $items
        ));
        $totalGst = array_sum(array_map(fn($i) => $i['gst'] * $i['quantity'], $items));
        $discount = $this->getDiscount($currency);
        $total = $subtotal + $totalGst - $discount;

        $subscriptionItems = array_values(array_filter($items, fn($i) => $i['is_subscription']));
        $upfrontItems = array_values(array_filter($items, fn($i) => !$i['is_subscription']));

        // A coupon only reaches the one-time lines. When the cart also holds a
        // subscription the customer needs to see that, or they may assume the
        // subscription was discounted too.
        $couponCode = $this->getCouponCode();
        $couponOnUpfrontOnly = $couponCode !== null && $subscriptionItems !== [] && $upfrontItems !== [];

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'gst' => $totalGst,
            'discount' => $discount,
            'total' => max(0, $total),
            'coupon_code' => $couponCode,
            'coupon_on_upfront_only' => $couponOnUpfrontOnly,
            'upfront_subtotal' => $this->getUpfrontSubtotal($currency),
            'billing_mode' => $subscriptionItems && !$upfrontItems
                ? self::MODE_SUBSCRIPTION
                : self::MODE_UPFRONT,
            'has_subscriptions' => $subscriptionItems !== [],
            'has_upfront' => $upfrontItems !== [],
            'is_mixed' => $subscriptionItems !== [] && $upfrontItems !== [],
            'subscription_count' => count($subscriptionItems),
            'upfront_count' => count($upfrontItems),
        ];
    }

    /**
     * The recurring charge for one cycle, before GST. The term price is spread
     * across the term's cycles; daily/weekly prices are already per-cycle.
     */
    public static function getCycleBase(float $termPrice, string $term): float
    {
        $cycle = self::billingCycle($term);

        return $cycle['per_cycle_price'] ? $termPrice : $termPrice / max(1, $cycle['cycles']);
    }

    /**
     * Per-cycle charge for a subscription line, including GST.
     */
    public static function getCycleAmount(float $termPrice, string $term, string $currency = 'INR'): float
    {
        $base = self::getCycleBase($termPrice, $term);
        $gst = $currency === 'INR' ? $base * self::GST_RATE : 0.0;

        return round($base + $gst, 2);
    }

    /**
     * Whether the cart holds anything a coupon is allowed to discount.
     *
     * Only the one-time lines qualify: a subscription is billed on a fixed
     * recurring plan amount, so it has no single charge to reduce.
     */
    public function hasUpfrontItems(): bool
    {
        foreach ($this->getItems() as $item) {
            if (self::normalizeBillingMode($item['billing_mode'] ?? null) !== self::MODE_SUBSCRIPTION) {
                return true;
            }
        }
        return false;
    }

    /**
     * Apply a coupon code. Returns true on success, error string on failure.
     *
     * A coupon discounts the one-time lines only. Subscriptions run on a fixed
     * recurring plan amount, which Razorpay charges identically on every
     * cycle, so there is no single charge for a coupon to reduce.
     */
    public function applyCoupon(string $code): bool|string
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();
        if (!$coupon) {
            return 'Invalid coupon code.';
        }

        if (!$this->hasUpfrontItems()) {
            return 'Coupons apply to one-time purchases. Your cart only contains subscriptions, which are billed on a fixed recurring plan.';
        }

        // Validate against what the coupon can actually discount, so a
        // subscription in the cart can neither trip the minimum order amount
        // nor satisfy a product restriction on its own.
        $subtotal = $this->getUpfrontSubtotal('INR');
        $result = $coupon->validateForCart($subtotal, $this->getUpfrontProductSlugs());

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
     * Discount for a specific set of one-time cart lines.
     *
     * An order normally covers every one-time line, matching getDiscount().
     * When it covers only some of them the discount has to be recomputed from
     * those lines, or the customer is credited more than the code is worth.
     *
     * @param  array<int, array>  $lines
     */
    public function getDiscountFor(array $lines, string $currency = 'INR'): float
    {
        $code = $this->getCouponCode();
        if (!$code) return 0;

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return 0;

        $subtotal = array_sum(array_map(
            fn($i) => (float) $i['unit_price'] * (int) $i['quantity'],
            $lines
        ));
        if ($subtotal <= 0) return 0;

        return $coupon->calculateDiscount($subtotal);
    }

    /**
     * Get discount amount in rupees.
     *
     * Scoped to the one-time lines. A subscription contributes its cycle price
     * to the cart subtotal for display, but it is not part of the amount a
     * coupon may reduce — including it would inflate the discount base and
     * under-charge the one-time order.
     */
    public function getDiscount(string $currency = 'INR'): float
    {
        $code = $this->getCouponCode();
        if (!$code) return 0;

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) return 0;

        $subtotal = $this->getUpfrontSubtotal($currency);
        if ($subtotal <= 0) return 0;

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
