<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Two related cart bugs:
 *
 *  1. The cart page hard-coded `billingMode = 'upfront'`, and the cart never
 *     stored a billing mode at all, so a subscription added on /buy was always
 *     settled as a one-time order from /cart.
 *  2. `handleCheckout()` only took the subscription path when the cart held
 *     exactly one line, so a second subscription product silently downgraded
 *     the whole cart to a one-time payment.
 *
 * Billing mode is now persisted per cart line, the cart page reports it, and a
 * cart may hold any number of subscription lines.
 */
class CartSubscriptionCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        Coupon::create([
            'code' => 'SAVE10',
            'description' => '10% off',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);
    }

    protected function makeProduct(string $slug, string $term, int $priceInr, int $priceUsd = 100): Product
    {
        $product = Product::create([
            'slug' => $slug,
            'sku' => strtoupper($slug),
            'name' => ucfirst($slug),
            'tier' => 'basic',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'term' => $term,
            'price_inr' => $priceInr,
            'price_usd' => $priceUsd,
            'is_active' => true,
        ]);

        return $product;
    }

    public function test_cart_page_reports_subscription_mode_for_a_subscription_cart(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');

        $this->assertSame('monthly', $contents['billing_mode'], 'Cart must keep the subscription mode.');
        $this->assertTrue($contents['has_subscriptions']);
        $this->assertFalse($contents['is_mixed']);
        $this->assertTrue($contents['items'][0]['is_subscription']);

        $this->get('/cart')->assertOk()->assertSee('Subscription');
    }

    public function test_a_cart_can_hold_several_subscription_products(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '1yr', 24000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $this->postJson('/api/cart/add', [
            'product_slug' => 'pro',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');

        $this->assertCount(2, $contents['items']);
        $this->assertSame(2, $contents['subscription_count']);
        $this->assertSame('monthly', $contents['billing_mode']);
    }

    public function test_the_same_product_can_be_queued_once_per_billing_mode(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        foreach (['upfront', 'monthly'] as $mode) {
            $this->postJson('/api/cart/add', [
                'product_slug' => 'spatial',
                'term' => '1yr',
                'billing_mode' => $mode,
            ])->assertOk();
        }

        $contents = app(CartService::class)->getContents('INR');

        $this->assertCount(2, $contents['items']);
        $this->assertTrue($contents['is_mixed']);
        $this->assertSame(1, $contents['subscription_count']);
        $this->assertSame(1, $contents['upfront_count']);

        // Removing one mode must leave the other line intact.
        $this->postJson('/api/cart/remove', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');
        $this->assertCount(1, $contents['items']);
        $this->assertFalse($contents['items'][0]['is_subscription']);
    }

    public function test_subscription_lines_are_always_a_single_seat(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'quantity' => 5,
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');
        $this->assertSame(1, $contents['items'][0]['quantity']);
    }

    public function test_cycle_amount_spreads_the_term_price_across_its_cycles(): void
    {
        // 1yr = 12 monthly charges: 12000 + 18% GST spread over 12 cycles.
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '1yr', 24000);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'pro', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();

        $items = app(CartService::class)->getContents('INR')['items'];

        // (12000 + 2160) / 12
        $this->assertEqualsWithDelta(1180.0, $items[0]['cycle_amount'], 0.01);
        // (24000 + 4320) / 12
        $this->assertEqualsWithDelta(2360.0, $items[1]['cycle_amount'], 0.01);
    }

    public function test_daily_price_is_already_a_cycle_price(): void
    {
        $this->makeProduct('spatial', 'daily', 200);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => 'daily', 'billing_mode' => 'monthly'])->assertOk();

        $item = app(CartService::class)->getContents('INR')['items'][0];

        // 200 + 18% GST, not divided by the 30 cycles.
        $this->assertEqualsWithDelta(236.0, $item['cycle_amount'], 0.01);
    }

    public function test_a_subscription_cart_totals_one_cycle_not_the_whole_term(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');

        // 1000 per month + 18% GST, charged today — not 12000 x 12.
        $this->assertEqualsWithDelta(1000.0, $contents['subtotal'], 0.01);
        $this->assertEqualsWithDelta(180.0, $contents['gst'], 0.01);
        $this->assertEqualsWithDelta(1180.0, $contents['total'], 0.01);
        $this->assertEqualsWithDelta(14160.0, $contents['items'][0]['term_total'], 0.01);
    }

    public function test_a_mixed_cart_totals_the_cycle_plus_the_one_time_price(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '6mo', 6000);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'pro', 'term' => '6mo', 'billing_mode' => 'upfront'])->assertOk();

        $contents = app(CartService::class)->getContents('INR');

        $this->assertTrue($contents['is_mixed']);
        // 1000 (one subscription cycle) + 6000 (one-time) before GST.
        $this->assertEqualsWithDelta(7000.0, $contents['subtotal'], 0.01);
        $this->assertEqualsWithDelta(1260.0, $contents['gst'], 0.01);
        $this->assertEqualsWithDelta(8260.0, $contents['total'], 0.01);
    }

    public function test_a_coupon_discounts_the_one_time_lines_of_a_mixed_cart(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);   // subscription
        $this->makeProduct('pro', '1yr', 24000);      // one-time

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'pro', 'term' => '1yr', 'billing_mode' => 'upfront'])->assertOk();

        // A mixed cart may now carry a coupon — it discounts the one-time half.
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])->assertOk();

        $contents = app(CartService::class)->getContents('INR');

        // 10% of the one-time subtotal only (24000), NOT of the cart subtotal
        // (24000 + the 1000 subscription cycle).
        $this->assertEqualsWithDelta(2400.0, $contents['discount'], 0.01);
        $this->assertTrue($contents['coupon_on_upfront_only']);
        $this->assertEqualsWithDelta(24000.0, $contents['upfront_subtotal'], 0.01);

        // 1000 (one cycle) + 24000 (one-time) = 25000 subtotal, 4500 GST,
        // less the 2400 coupon.
        $this->assertEqualsWithDelta(25000.0, $contents['subtotal'], 0.01);
        $this->assertEqualsWithDelta(27100.0, $contents['total'], 0.01);
    }

    public function test_a_coupon_is_kept_when_a_subscription_is_added_later(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'upfront'])->assertOk();
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])->assertOk();

        $this->assertSame('SAVE10', app(CartService::class)->getCouponCode());

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        // The coupon survives: a subscription cannot inflate the amount it
        // discounts, so there is no reason to discard it.
        $this->assertSame('SAVE10', app(CartService::class)->getCouponCode());

        $contents = app(CartService::class)->getContents('INR');
        $this->assertTrue($contents['is_mixed']);
        $this->assertTrue($contents['coupon_on_upfront_only']);
        // 10% of the 12000 one-time line only.
        $this->assertEqualsWithDelta(1200.0, $contents['discount'], 0.01);
    }

    public function test_a_coupon_is_refused_when_the_cart_is_subscriptions_only(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        // There is no one-time charge for a coupon to reduce.
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])
            ->assertStatus(422)
            ->assertJsonStructure(['error']);

        $this->assertNull(app(CartService::class)->getCouponCode());
    }

    public function test_a_subscription_cannot_satisfy_a_coupon_minimum_on_its_own(): void
    {
        $this->makeProduct('spatial', '1yr', 100000);  // subscription, large
        $this->makeProduct('pro', '1yr', 400);         // one-time, below the minimum

        Coupon::where('code', 'SAVE10')->first()->update(['min_order_amount' => 50000]); // paise = ₹500

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'pro', 'term' => '1yr', 'billing_mode' => 'upfront'])->assertOk();

        // The cart subtotal clears the minimum on the strength of the
        // subscription, but the coupon may only discount the one-time line.
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])
            ->assertStatus(422)
            ->assertJsonStructure(['error']);
    }

    public function test_carts_written_before_billing_modes_still_load(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        session()->put('cart.items', [
            'spatial|1yr' => ['product_slug' => 'spatial', 'term' => '1yr', 'quantity' => 2],
        ]);

        $contents = app(CartService::class)->getContents('INR');

        $this->assertCount(1, $contents['items']);
        $this->assertFalse($contents['items'][0]['is_subscription']);
        $this->assertSame(2, $contents['items'][0]['quantity']);
        $this->assertSame('upfront', $contents['billing_mode']);
    }

    public function test_upfront_quantity_updates_do_not_touch_subscription_lines(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'upfront'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();

        $this->postJson('/api/cart/update', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'quantity' => 4,
            'billing_mode' => 'upfront',
        ])->assertOk();

        $items = app(CartService::class)->getContents('INR')['items'];

        $this->assertCount(2, $items);
        $byLine = [];
        foreach ($items as $item) {
            $byLine[$item['billing_mode']] = $item['quantity'];
        }
        $this->assertSame(4, $byLine['upfront']);
        $this->assertSame(1, $byLine['monthly']);
    }

    /**
     * The cart page renders from the server, but every mutation refetches via
     * GET /api/cart. That response is flat rather than nested under
     * `contents`, so it has to carry the billing flags too — otherwise the
     * coupon box disappears from one-time carts after the first refetch.
     */
    public function test_the_cart_endpoint_reports_billing_flags_for_a_one_time_cart(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'upfront',
        ])->assertOk();

        $this->getJson('/api/cart')
            ->assertOk()
            ->assertJson([
                'billing_mode' => 'upfront',
                'has_subscriptions' => false,
                'has_upfront' => true,
                'is_mixed' => false,
                'item_count' => 1,
            ]);
    }

    public function test_the_cart_endpoint_reports_billing_flags_for_a_mixed_cart(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '1yr', 24000);

        $this->postJson('/api/cart/add', ['product_slug' => 'spatial', 'term' => '1yr', 'billing_mode' => 'monthly'])->assertOk();
        $this->postJson('/api/cart/add', ['product_slug' => 'pro', 'term' => '1yr', 'billing_mode' => 'upfront'])->assertOk();

        $this->getJson('/api/cart')
            ->assertOk()
            ->assertJson([
                'billing_mode' => 'upfront',
                'has_subscriptions' => true,
                'has_upfront' => true,
                'is_mixed' => true,
                'item_count' => 2,
            ]);
    }

    /**
     * The buy page quotes a per-cycle figure. It is read from /api/pricing
     * rather than recomputed in JS, so the quote and the charge the cart
     * settles at cannot drift apart.
     */
    public function test_the_pricing_endpoint_quotes_the_same_cycle_amount_the_cart_charges(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);

        $price = $this->getJson('/api/pricing?currency=INR')
            ->assertOk()
            ->json('products.spatial.prices.1yr');

        $this->assertEqualsWithDelta(12000, $price['amount'], 0.01);
        $this->assertEqualsWithDelta(1000, $price['cycle_base'], 0.01);
        $this->assertEqualsWithDelta(1180, $price['cycle_amount'], 0.01);
        $this->assertSame(12, $price['cycle_count']);
        $this->assertSame('monthly', $price['cycle_period']);
        $this->assertSame(1, $price['cycle_interval']);

        // The cart settles the subscription line at exactly that cycle amount.
        $this->postJson('/api/cart/add', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'billing_mode' => 'monthly',
        ])->assertOk();

        $contents = app(CartService::class)->getContents('INR');
        $this->assertEqualsWithDelta(
            $price['cycle_amount'],
            $contents['items'][0]['cycle_amount'],
            0.01,
            'The buy page quote must match what checkout actually charges.'
        );
        $this->assertEqualsWithDelta(
            $price['cycle_amount'] * 100,
            $contents['total'] * 100,
            1,
            'The amount due today must be one cycle of the quoted plan.'
        );
    }

    public function test_the_pricing_endpoint_does_not_divide_a_daily_price(): void
    {
        $this->makeProduct('spatial', 'daily', 200);

        // A daily price is already a per-cycle price.
        $price = $this->getJson('/api/pricing?currency=INR')
            ->assertOk()
            ->json('products.spatial.prices.daily');

        $this->assertEqualsWithDelta(200, $price['cycle_base'], 0.01);
        $this->assertEqualsWithDelta(236, $price['cycle_amount'], 0.01);
        $this->assertSame(30, $price['cycle_count']);
        $this->assertSame('daily', $price['cycle_period']);
    }

    public function test_a_usd_quote_carries_no_gst(): void
    {
        $this->makeProduct('spatial', '1yr', 12000, 220);

        $price = $this->getJson('/api/pricing?currency=USD')
            ->assertOk()
            ->json('products.spatial.prices.1yr');

        $this->assertEqualsWithDelta(220, $price['amount'], 0.01);
        $this->assertEqualsWithDelta(220 / 12, $price['cycle_base'], 0.01);
        $this->assertEqualsWithDelta(220 / 12, $price['cycle_amount'], 0.01);
    }
}
