<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCoupon;
use App\Models\Subscription;
use App\Models\LicenseKey;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CartService;
use App\Services\RazorpayService;
use App\Mail\OrderConfirmation;
use App\Mail\SubscriptionConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RazorpayController extends Controller
{
    public function __construct(
        protected RazorpayService $razorpay
    ) {}

    /**
     * Create a Razorpay order for one-time payment.
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
            'amount' => 'required|integer|min:100',
            'currency' => 'required|string|size:3',
        ]);

        $product = Product::where('slug', $request->product_slug)->firstOrFail();

        $notes = [
            'sku' => $product->sku ?? '',
            'term_months' => (string) termMonths($request->term),
            'customer_name' => Auth::user()->name ?? '',
            'customer_email' => Auth::user()->email ?? '',
        ];

        $order = $this->razorpay->createOrder(
            $request->amount,
            $request->currency,
            $notes
        );

        if (isset($order['error'])) {
            return response()->json(['error' => $order['error']], 500);
        }

        // Save order in database
        $dbOrder = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'term' => $request->term,
            'currency' => $request->currency,
            'amount' => $request->amount / ($request->currency === 'INR' ? 1 : 100),
            'gst_amount' => 0,
            'total_amount' => $request->amount / ($request->currency === 'INR' ? 1 : 100),
            'razorpay_order_id' => $order['id'],
            'status' => 'pending',
            'billing_mode' => 'upfront',
        ]);

        return response()->json([
            'id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'db_order_id' => $dbOrder->id,
        ]);
    }

    /**
     * Create a Razorpay order from the shopping cart.
     *
     * Subscription lines cannot ride on a one-time order, so a cart may carry
     * an `items` list naming the one-time lines this order should cover. That
     * lets a mixed cart (subscriptions + one-time purchases) settle the
     * one-time half in a single order after the subscriptions are confirmed.
     */
    public function createCartOrder(Request $request)
    {
        $request->validate([
            'currency' => 'nullable|string|size:3',
            'coupon_code' => 'nullable|string|max:50',
            'items' => 'nullable|array',
            'items.*.product_slug' => 'required|string',
            'items.*.term' => 'required|string',
        ]);

        $cart = app(CartService::class);
        $currency = $request->input('currency', 'INR');
        $contents = $cart->getContents($currency);

        if (empty($contents['items'])) {
            return response()->json(['error' => 'Cart is empty.'], 422);
        }

        // Subscription lines are settled by their own plan/subscription, so
        // they are never included in a one-time order.
        $lines = $this->resolveUpfrontLines($contents['items'], $request->input('items'));

        if (empty($lines)) {
            return response()->json(['error' => 'No one-time items to pay for in this cart.'], 422);
        }

        $subtotal = array_sum(array_map(fn($i) => $i['unit_price'] * $i['quantity'], $lines));
        $totalGst = array_sum(array_map(fn($i) => $i['gst'] * $i['quantity'], $lines));

        // A coupon only discounts the one-time lines, so compute it from the
        // lines this order actually covers rather than the whole cart.
        $discount = min(
            $cart->getDiscountFor($lines, $currency),
            $subtotal + $totalGst
        );
        $totalPaise = toPaise($subtotal + $totalGst - $discount);

        if ($totalPaise < 100) {
            return response()->json(['error' => 'Order total is too low to process.'], 422);
        }

        $notes = [
            'items' => count($lines),
            'customer_name' => Auth::user()->name ?? '',
            'customer_email' => Auth::user()->email ?? '',
        ];

        if ($discount > 0) {
            $notes['coupon'] = $contents['coupon_code'];
        }

        $order = $this->razorpay->createOrder($totalPaise, $currency, $notes);

        if (isset($order['error'])) {
            return response()->json(['error' => $order['error']], 500);
        }

        $discountPaise = (int) round($discount * 100);
        $subtotalPaise = (int) round($subtotal * 100);
        $gstPaise = (int) round($totalGst * 100);

        // Determine the most common term for the order record
        $termCounts = array_count_values(array_column($lines, 'term'));
        arsort($termCounts);
        $dominantTerm = array_key_first($termCounts);

        $dbOrder = Order::create([
            'user_id' => Auth::id(),
            'product_id' => null,
            'term' => $dominantTerm,
            'currency' => $currency,
            'amount' => $subtotalPaise,
            'gst_amount' => $gstPaise,
            'total_amount' => $totalPaise,
            'razorpay_order_id' => $order['id'],
            'status' => 'pending',
            'billing_mode' => 'upfront',
            'coupon_code' => $discount > 0 ? $contents['coupon_code'] : null,
            'discount_amount' => $discountPaise,
        ]);

        // Create order items
        foreach ($lines as $item) {
            $product = Product::where('slug', $item['product_slug'])->first();
            if (!$product) continue;

            OrderItem::create([
                'order_id' => $dbOrder->id,
                'product_id' => $product->id,
                'term' => $item['term'],
                'quantity' => $item['quantity'],
                'unit_price_inr' => (int) round($item['unit_price'] * 100),
                'gst_amount' => (int) round($item['gst'] * 100),
                'total_amount' => (int) round($item['line_total'] * 100),
            ]);
        }

        // Record coupon usage
        if ($discount > 0 && $contents['coupon_code']) {
            $coupon = \App\Models\Coupon::where('code', $contents['coupon_code'])->first();
            if ($coupon) {
                OrderCoupon::create([
                    'order_id' => $dbOrder->id,
                    'coupon_id' => $coupon->id,
                    'discount' => $discountPaise,
                ]);
                $coupon->incrementUsage();
            }
        }

        // The purchased lines stay in the cart until the payment is verified.
        // Dropping them here would lose the items if the customer dismisses
        // the Razorpay modal.

        return response()->json([
            'id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'db_order_id' => $dbOrder->id,
        ]);
    }

    /**
     * Pick the one-time cart lines this order should cover. When the client
     * sends no `items` list, every one-time line in the cart is used.
     *
     * @return array<int, array>
     */
    protected function resolveUpfrontLines(array $items, ?array $requested): array
    {
        $upfront = array_values(array_filter($items, fn($i) => empty($i['is_subscription'])));

        if (!$requested) {
            return $upfront;
        }

        $keys = [];
        foreach ($requested as $entry) {
            $keys[$entry['product_slug'] . '|' . $entry['term']] = true;
        }

        return array_values(array_filter(
            $upfront,
            fn($i) => isset($keys[$i['product_slug'] . '|' . $i['term']])
        ));
    }

    /**
     * Verify payment signature after checkout.
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'db_order_id' => 'required|integer',
        ]);

        $isValid = $this->razorpay->verifyPaymentSignature($request->only([
            'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
        ]));

        if (!$isValid) {
            return response()->json(['success' => false, 'message' => 'Invalid payment signature'], 400);
        }

        $order = Order::findOrFail($request->db_order_id);
        $order->update([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'status' => 'paid',
        ]);

        // Only now that the payment has settled do the purchased lines leave
        // the cart. Anything still queued was never bought and must survive.
        $cart = app(CartService::class);
        foreach ($order->orderItems as $item) {
            $slug = $item->product?->slug;
            if ($slug) {
                $cart->removeItem($slug, $item->term, CartService::MODE_UPFRONT);
            }
        }

        if (empty($cart->getItems())) {
            $cart->clear();
        }

        // Issue license key
        // Disabled — license keys are now issued by the external license server
        // (https://license.infyterra.net) via Razorpay webhooks (payment.captured).
        // $this->issueLicenseKey($order);

        // Send confirmation email
        try {
            Mail::to($order->user->email)->send(new OrderConfirmation($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send order email', ['error' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'payment_id' => $request->razorpay_payment_id]);
    }

    /**
     * Create a Razorpay subscription.
     */
    public function createSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|string',
            'product_slug' => 'required|string',
            'term' => 'required|string',
        ]);

        $subscription = $this->razorpay->createSubscription($request->plan_id);

        if (isset($subscription['error'])) {
            return response()->json(['error' => $subscription['error']], 500);
        }

        $product = Product::where('slug', $request->product_slug)->first();

        $unitPrice = $product?->getPriceForTerm($request->term);
        $amount = $unitPrice !== null
            ? CartService::getCycleAmount((float) $unitPrice, $request->term)
            : 0;

        Subscription::create([
            'user_id' => Auth::id(),
            'product_id' => $product?->id,
            'term' => $request->term,
            'razorpay_subscription_id' => $subscription['id'],
            'razorpay_plan_id' => $request->plan_id,
            'status' => 'active',
            'amount' => $amount,
            'currency' => 'INR',
        ]);

        return response()->json([
            'id' => $subscription['id'],
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Create a Razorpay Plan + Subscription for one cart line.
     *
     * A cart may hold several subscription products, so this is called once
     * per line and the resulting subscriptions are settled one after another.
     * The plan amount is derived from the product price on the server — a
     * Razorpay plan is charged on *every* cycle, so the client must not be
     * able to choose it.
     */
    public function createPlanAndSubscription(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
        ]);

        $product = Product::where('slug', $request->product_slug)->firstOrFail();
        $term = $request->term;
        $currency = 'INR';

        $unitPrice = $product->getPriceForTerm($term, $currency);
        if ($unitPrice === null) {
            return response()->json(['error' => 'This product is not available for the selected term.'], 422);
        }

        $cycle = CartService::billingCycle($term);
        $cycleAmount = CartService::getCycleAmount((float) $unitPrice, $term, $currency);
        $cycleAmountPaise = toPaise($cycleAmount);

        if ($cycleAmountPaise < 100) {
            return response()->json(['error' => 'Subscription amount is too low to process.'], 422);
        }

        $notes = [
            'sku' => $product->sku ?? '',
            'term_months' => (string) termMonths($term),
            'customer_name' => Auth::user()->name ?? '',
            'customer_email' => Auth::user()->email ?? '',
        ];

        // Create plan
        $planResult = $this->razorpay->createPlan(
            $cycleAmountPaise,
            $currency,
            $cycle['period'],
            $cycle['interval'],
            $product->name . ' — ' . termLabel($term),
            'AutoTerra ' . $product->name . ' subscription',
            $notes
        );

        if (isset($planResult['error'])) {
            return response()->json(['error' => $planResult['error']], 500);
        }

        // Create subscription using the plan
        $subResult = $this->razorpay->createSubscription($planResult['id'], $cycle['cycles'], $notes);

        if (isset($subResult['error'])) {
            return response()->json(['error' => $subResult['error']], 500);
        }

        // Save subscription in database
        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'term' => $term,
            'razorpay_subscription_id' => $subResult['id'],
            'razorpay_plan_id' => $planResult['id'],
            'status' => 'active',
            'amount' => $cycleAmount,
            'currency' => $currency,
        ]);

        return response()->json([
            'subscription_id' => $subResult['id'],
            'plan_id' => $planResult['id'],
            'db_subscription_id' => $subscription->id,
            'product_name' => $product->name,
            'term_label' => termLabel($term),
            'amount' => $cycleAmountPaise,
        ]);
    }

    /**
     * Cancel a pending subscription that was never paid.
     */
    public function cancelPendingSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string',
        ]);

        $sub = Subscription::where('razorpay_subscription_id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($sub) {
            if ($sub->razorpay_subscription_id) {
                $this->razorpay->cancelSubscription($sub->razorpay_subscription_id);
            }
            $sub->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Cancel a pending order that was never paid.
     */
    public function cancelPendingOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($order) {
            $order->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Verify the first payment of a subscription.
     */
    public function verifySubscription(Request $request)
    {
        $request->validate([
            'razorpay_subscription_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $isValid = $this->razorpay->verifyPaymentSignature(array_merge(
            $request->only(['razorpay_subscription_id', 'razorpay_payment_id', 'razorpay_signature']),
            ['is_subscription' => true]
        ));

        if (!$isValid) {
            return response()->json(['success' => false, 'message' => 'Invalid subscription signature'], 400);
        }

        // Scoped to the signed-in user so a signature for someone else's
        // subscription cannot be replayed here.
        $sub = Subscription::where('razorpay_subscription_id', $request->razorpay_subscription_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$sub) {
            return response()->json(['success' => false, 'message' => 'Subscription not found'], 404);
        }

        $periodEnd = now()->addDays(termDays($sub->term));

        $sub->update([
            'status' => 'active',
            'current_period_start' => $sub->current_period_start ?? now(),
            'current_period_end' => $periodEnd,
        ]);

        // Send subscription confirmation email
        try {
            Mail::to($sub->user->email)->send(new SubscriptionConfirmation($sub));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send subscription email', ['error' => $e->getMessage()]);
        }

        return response()->json(['success' => true, 'subscription_id' => $request->razorpay_subscription_id]);
    }

    /**
     * Razorpay webhook handler.
     */
    public function webhook(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature', '');

        if (!$this->razorpay->verifyWebhookSignature($rawBody, $signature)) {
            return response('Invalid signature', 400);
        }

        $event = json_decode($rawBody, true);
        $eventName = $event['event'] ?? '';
        $payload = $event['payload'] ?? [];

        switch ($eventName) {
            case 'payment.captured':
                $payment = $payload['payment']['entity'] ?? [];
                $orderId = $payment['order_id'] ?? '';
                $paymentId = $payment['id'] ?? '';
                $order = Order::where('razorpay_order_id', $orderId)->first();
                if ($order && $order->status !== 'paid') {
                    $order->update(['razorpay_payment_id' => $paymentId, 'status' => 'paid']);
                    // Disabled — license keys are now issued by the external license server
                    // (https://license.infyterra.net) via Razorpay webhooks.
                    // $this->issueLicenseKey($order);
                }
                break;

            case 'subscription.activated':
                $sub = $payload['subscription']['entity'] ?? [];
                $subId = $sub['id'] ?? '';
                Subscription::where('razorpay_subscription_id', $subId)
                    ->update(['status' => 'active']);
                break;

            case 'subscription.charged':
                // Record renewal
                break;

            case 'subscription.cancelled':
                $sub = $payload['subscription']['entity'] ?? [];
                $subId = $sub['id'] ?? '';
                Subscription::where('razorpay_subscription_id', $subId)
                    ->update(['status' => 'cancelled']);
                break;

            case 'payment.failed':
                $payment = $payload['payment']['entity'] ?? [];
                $orderId = $payment['order_id'] ?? '';
                Order::where('razorpay_order_id', $orderId)
                    ->update(['status' => 'failed']);
                break;
        }

        return response('OK', 200);
    }

    /**
     * Issue a license key for a paid order.
     */
    protected function issueLicenseKey(Order $order): void
    {
        if (Setting::get('license_key_mode', 'auto') === 'manual') {
            return;
        }

        LicenseKey::create([
            'user_id' => $order->user_id,
            'product_id' => $order->product_id,
            'order_id' => $order->id,
            'license_key' => Str::uuid()->toString(),
            'activated_at' => now(),
            'expires_at' => now()->addDays(termDays($order->term)),
            'is_active' => true,
            'max_activations' => 1,
        ]);
    }
}
