<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Server side of the subscription cart flow.
 *
 * A cart may hold several subscription lines and any number of one-time lines.
 * Checkout settles subscriptions first — one Razorpay plan and subscription per
 * line, each at that line's own per-cycle amount — and only then creates a
 * single one-time order for the remaining lines.
 *
 * The plan amount is a recurring charge repeated on every cycle, so it is
 * derived from the product price on the server. Trusting the client here meant
 * charging the whole cart total once per cycle.
 *
 * @see \Tests\Feature\CartSubscriptionCheckoutTest for the cart-line rules.
 */
class SubscriptionCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
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

    protected function addLine(string $slug, string $term, string $mode = 'monthly'): void
    {
        $this->postJson('/api/cart/add', [
            'product_slug' => $slug,
            'term' => $term,
            'billing_mode' => $mode,
        ])->assertOk();
    }

    protected function fakeRazorpay(): void
    {
        Http::fake([
            'api.razorpay.com/v1/plans' => Http::response(['id' => 'plan_1', 'name' => 'p'], 200),
            'api.razorpay.com/v1/subscriptions' => Http::response([
                'id' => 'sub_1',
                'status' => 'created',
            ], 200),
            'api.razorpay.com/v1/orders' => Http::response([
                'id' => 'order_1',
                'amount' => 1,
                'currency' => 'INR',
            ], 200),
        ]);
    }

    public function test_a_subscription_plan_is_charged_one_cycle_not_the_cart_total(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '1yr', 24000);
        $this->fakeRazorpay();

        // A second line is in the cart, so the old code would have used the
        // whole cart total as the plan amount.
        $this->addLine('pro', '1yr', 'upfront');

        $this->postJson('/api/razorpay/create-plan', [
            'product_slug' => 'spatial',
            'term' => '1yr',
        ])->assertOk();

        // 12000 / 12 = 1000 per month, +18% GST = 1180.
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/plans'
                && $request['item']['amount'] === 118000
                && $request['period'] === 'monthly'
                && $request['interval'] === 1;
        });
    }

    public function test_a_client_supplied_plan_amount_is_ignored(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->fakeRazorpay();

        $this->postJson('/api/razorpay/create-plan', [
            'product_slug' => 'spatial',
            'term' => '1yr',
            'amount' => 1,
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/plans'
                && $request['item']['amount'] === 118000;
        });
    }

    public function test_each_subscription_line_gets_its_own_plan_at_its_own_amount(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);   // 1000/mo -> 1180
        $this->makeProduct('pro', '6mo', 9500);        // 9500 per 6mo -> 11210
        $this->fakeRazorpay();

        $this->addLine('spatial', '1yr');
        $this->addLine('pro', '6mo');

        // The cart page settles each subscription line in turn.
        $this->postJson('/api/razorpay/create-plan', ['product_slug' => 'spatial', 'term' => '1yr'])->assertOk();
        $this->postJson('/api/razorpay/create-plan', ['product_slug' => 'pro', 'term' => '6mo'])->assertOk();

        $amounts = [];
        Http::assertSent(function ($request) use (&$amounts) {
            if ($request->url() !== 'https://api.razorpay.com/v1/plans') return true;
            $amounts[] = $request['item']['amount'];
            return true;
        });

        $this->assertSame([118000, 1121000], $amounts);

        // Two separate subscription records, each at its own cycle amount.
        $this->assertSame(2, Subscription::where('user_id', $this->user->id)->count());
        $this->assertEqualsWithDelta(
            1180,
            Subscription::where('user_id', $this->user->id)->orderBy('id')->first()->amount,
            0.01
        );
    }

    public function test_a_daily_plan_is_not_divided_by_the_number_of_days(): void
    {
        $this->makeProduct('spatial', 'daily', 200);
        $this->fakeRazorpay();

        $this->postJson('/api/razorpay/create-plan', [
            'product_slug' => 'spatial',
            'term' => 'daily',
        ])->assertOk();

        // 200 + 18% GST = 236 per day, charged 30 times.
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/plans'
                && $request['item']['amount'] === 23600
                && $request['period'] === 'daily';
        });

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/subscriptions'
                && $request['total_count'] === 30;
        });
    }

    public function test_a_one_time_order_covers_only_the_one_time_lines(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->makeProduct('pro', '1yr', 24000);
        $this->fakeRazorpay();

        $this->addLine('spatial', '1yr', 'monthly');
        $this->addLine('pro', '1yr', 'upfront');

        $this->postJson('/api/razorpay/create-cart-order', [
            'currency' => 'INR',
            'items' => [
                ['product_slug' => 'pro', 'term' => '1yr'],
            ],
        ])->assertOk();

        // Only the one-time line: 24000 + 18% GST = 28320.
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/orders'
                && $request['amount'] === 2832000;
        });

        // Nothing has been paid yet, so both lines are still queued. The
        // subscription line in particular must never ride on this order.
        $contents = app(CartService::class)->getContents('INR');
        $this->assertCount(2, $contents['items']);
        $this->assertSame(
            ['monthly', 'upfront'],
            array_column($contents['items'], 'billing_mode')
        );

        $order = Order::firstOrFail();
        $this->assertSame(1, $order->orderItems()->count());
        $this->assertSame('pro', $order->orderItems()->first()->product->slug);
    }

    public function test_purchased_lines_leave_the_cart_only_once_payment_succeeds(): void
    {
        $product = $this->makeProduct('pro', '1yr', 24000);
        $this->fakeRazorpay();

        $this->addLine('pro', '1yr', 'upfront');

        $order = $this->postJson('/api/razorpay/create-cart-order', ['currency' => 'INR'])
            ->assertOk()
            ->json('db_order_id');

        // Creating the order is not a purchase — dismissing Razorpay must not
        // lose the item.
        $this->assertCount(1, app(CartService::class)->getContents('INR')['items']);

        $signature = hash_hmac('sha256', 'order_1|pay_1', config('razorpay.key_secret'));

        $this->postJson('/api/razorpay/verify', [
            'razorpay_order_id' => 'order_1',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => $signature,
            'db_order_id' => $order,
        ])->assertOk();

        $this->assertCount(0, app(CartService::class)->getContents('INR')['items']);
    }

    public function test_a_failed_payment_leaves_the_cart_untouched(): void
    {
        $this->makeProduct('pro', '1yr', 24000);
        $this->fakeRazorpay();

        $this->addLine('pro', '1yr', 'upfront');

        $this->postJson('/api/razorpay/create-cart-order', ['currency' => 'INR'])->assertOk();

        // A bogus signature never reaches the cart-cleanup step.
        $this->postJson('/api/razorpay/verify', [
            'razorpay_order_id' => 'order_1',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => 'not-the-signature',
            'db_order_id' => Order::firstOrFail()->id,
        ])->assertStatus(400);

        $this->assertCount(1, app(CartService::class)->getContents('INR')['items']);
    }

    public function test_a_one_time_order_never_contains_a_subscription_line(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->fakeRazorpay();

        $this->addLine('spatial', '1yr', 'monthly');

        // No `items` subset, so every one-time line would be used — there are
        // none, so this must be refused rather than charging the cycle as a
        // one-time order.
        $this->postJson('/api/razorpay/create-cart-order', ['currency' => 'INR'])
            ->assertStatus(422)
            ->assertJsonStructure(['error']);

        Http::assertNothingSent();
        $this->assertSame(0, Order::count());
    }

    public function test_a_one_time_order_amount_comes_from_the_server_not_the_client(): void
    {
        $this->makeProduct('pro', '1yr', 24000);
        $this->fakeRazorpay();

        $this->addLine('pro', '1yr', 'upfront');

        $this->postJson('/api/razorpay/create-cart-order', [
            'currency' => 'INR',
            'amount' => 1,
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/orders'
                && $request['amount'] === 2832000;
        });
    }

    public function test_a_coupon_discounts_only_the_one_time_lines(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);  // subscription
        $this->makeProduct('pro', '1yr', 24000);     // one-time, 10% off
        $this->fakeRazorpay();

        \App\Models\Coupon::create([
            'code' => 'SAVE10',
            'description' => '10% off',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $this->addLine('spatial', '1yr', 'monthly');
        $this->addLine('pro', '1yr', 'upfront');

        // A coupon now applies to a mixed cart, but only to the one-time half.
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])->assertOk();

        $this->postJson('/api/razorpay/create-cart-order', [
            'currency' => 'INR',
            'items' => [['product_slug' => 'pro', 'term' => '1yr']],
        ])->assertOk();

        // 24000 + 18% GST = 28320, less 10% of the 24000 one-time line = 25920.
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/orders'
                && $request['amount'] === 2592000;
        });
    }

    public function test_a_coupon_is_refused_when_the_cart_holds_no_one_time_lines(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);
        $this->fakeRazorpay();

        \App\Models\Coupon::create([
            'code' => 'SAVE10',
            'description' => '10% off',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);

        $this->addLine('spatial', '1yr', 'monthly');

        // There is no one-time charge in the cart for a coupon to reduce.
        $this->postJson('/api/cart/coupon/apply', ['code' => 'SAVE10'])
            ->assertStatus(422)
            ->assertJsonStructure(['error']);
    }

    public function test_verifying_a_subscription_only_works_for_the_signed_in_user(): void
    {
        $product = $this->makeProduct('spatial', '1yr', 12000);

        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'term' => '1yr',
            'razorpay_subscription_id' => 'sub_someone_else',
            'razorpay_plan_id' => 'plan_1',
            'status' => 'active',
            'amount' => 1180,
            'currency' => 'INR',
        ]);

        // Razorpay signs a subscription payment as paymentId|subscriptionId.
        $signature = hash_hmac(
            'sha256',
            'pay_1|sub_someone_else',
            config('razorpay.key_secret')
        );

        $other = User::factory()->create();
        $this->actingAs($other);

        // The signature is genuine, but it is not this user's subscription.
        $this->postJson('/api/razorpay/verify-subscription', [
            'razorpay_subscription_id' => 'sub_someone_else',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => $signature,
        ])->assertStatus(404);

        $this->assertNull($subscription->fresh()->current_period_end);
    }

    public function test_the_same_product_can_be_bought_one_time_and_by_subscription(): void
    {
        // The hardest mixed case: one slug+term present in the cart under both
        // billing modes at the same time.
        $this->makeProduct('spatial', '1yr', 12000);
        $this->fakeRazorpay();

        $this->addLine('spatial', '1yr', 'monthly');
        $this->addLine('spatial', '1yr', 'upfront');

        // Step 1 — the subscription settles on its own recurring plan.
        $this->postJson('/api/razorpay/create-plan', [
            'product_slug' => 'spatial',
            'term' => '1yr',
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/plans'
                && $request['item']['amount'] === 118000;   // 1000/mo + 18% GST
        });

        // Step 2 — the one-time line settles on its own order, at the full
        // term price, untouched by the subscription.
        $this->postJson('/api/razorpay/create-cart-order', [
            'currency' => 'INR',
            'items' => [['product_slug' => 'spatial', 'term' => '1yr']],
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/orders'
                && $request['amount'] === 1416000;          // 12000 + 18% GST
        });

        // The order must not have absorbed the subscription line.
        $order = Order::firstOrFail();
        $this->assertSame(1, $order->orderItems()->count());
        $this->assertSame('1yr', $order->orderItems()->first()->term);
    }

    public function test_two_subscriptions_and_one_one_time_line_settle_independently(): void
    {
        $this->makeProduct('spatial', '1yr', 12000);  // 1000/mo  -> 1180
        $this->makeProduct('pro', '6mo', 9500);       // 9500/6mo -> 11210
        $this->makeProduct('std', '1yr', 15000);      // 1250/mo  -> 1475
        $this->fakeRazorpay();

        $this->addLine('spatial', '1yr', 'monthly');
        $this->addLine('pro', '6mo', 'monthly');
        $this->addLine('std', '1yr', 'upfront');

        // Each subscription line gets its own plan at its own amount.
        $this->postJson('/api/razorpay/create-plan', ['product_slug' => 'spatial', 'term' => '1yr'])->assertOk();
        $this->postJson('/api/razorpay/create-plan', ['product_slug' => 'pro', 'term' => '6mo'])->assertOk();

        $planAmounts = [];
        Http::assertSent(function ($request) use (&$planAmounts) {
            if ($request->url() !== 'https://api.razorpay.com/v1/plans') return true;
            $planAmounts[] = $request['item']['amount'];
            return true;
        });
        $this->assertSame([118000, 1121000], $planAmounts);
        $this->assertSame(2, Subscription::count());

        // Then the one-time line, as a single order.
        $this->postJson('/api/razorpay/create-cart-order', [
            'currency' => 'INR',
            'items' => [['product_slug' => 'std', 'term' => '1yr']],
        ])->assertOk();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.razorpay.com/v1/orders'
                && $request['amount'] === 1770000;          // 15000 + 18% GST
        });

        $this->assertSame(1, Order::firstOrFail()->orderItems()->count());
    }

    public function test_verifying_a_subscription_records_its_current_period(): void    {
        $product = $this->makeProduct('spatial', '1yr', 12000);

        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'term' => '1yr',
            'razorpay_subscription_id' => 'sub_mine',
            'razorpay_plan_id' => 'plan_1',
            'status' => 'active',
            'amount' => 1180,
            'currency' => 'INR',
        ]);

        $signature = hash_hmac('sha256', 'pay_1|sub_mine', config('razorpay.key_secret'));

        $this->postJson('/api/razorpay/verify-subscription', [
            'razorpay_subscription_id' => 'sub_mine',
            'razorpay_payment_id' => 'pay_1',
            'razorpay_signature' => $signature,
        ])->assertOk();

        $subscription->refresh();

        $this->assertNotNull($subscription->current_period_start);
        $this->assertNotNull($subscription->current_period_end);
        // A 1yr term runs for 365 days.
        $days = (int) now()->startOfDay()->diffInDays(
            $subscription->current_period_end->startOfDay(),
            true
        );
        $this->assertSame(365, $days);
    }
}
