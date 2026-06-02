<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\LicenseKey;
use App\Models\Product;
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

        $order = $this->razorpay->createOrder(
            $request->amount,
            $request->currency,
            ['product_name' => $product->name, 'term' => $request->term]
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

        // Issue license key
        $this->issueLicenseKey($order);

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

        Subscription::create([
            'user_id' => Auth::id(),
            'product_id' => $product?->id,
            'term' => $request->term,
            'razorpay_subscription_id' => $subscription['id'],
            'razorpay_plan_id' => $request->plan_id,
            'status' => 'active',
        ]);

        return response()->json([
            'id' => $subscription['id'],
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Create a Razorpay Plan + Subscription in one step.
     * Used by the buy page when subscription mode is selected.
     */
    public function createPlanAndSubscription(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
        ]);

        $product = Product::where('slug', $request->product_slug)->firstOrFail();

        // Determine billing period
        $periodMap = [
            'daily' => ['period' => 'daily', 'interval' => 1],
            'weekly' => ['period' => 'weekly', 'interval' => 1],
            '3mo' => ['period' => 'monthly', 'interval' => 3],
            '6mo' => ['period' => 'monthly', 'interval' => 6],
            '1yr' => ['period' => 'yearly', 'interval' => 1],
            '3yr' => ['period' => 'yearly', 'interval' => 3],
            '5yr' => ['period' => 'yearly', 'interval' => 5],
        ];
        $billing = $periodMap[$request->term] ?? ['period' => 'monthly', 'interval' => 1];

        // Create plan
        $planResult = $this->razorpay->createPlan(
            toPaise($request->amount),
            $request->currency,
            $billing['period'],
            $billing['interval'],
            $product->name . ' — ' . termLabel($request->term),
            'AutoTerra ' . $product->name . ' subscription'
        );

        if (isset($planResult['error'])) {
            return response()->json(['error' => $planResult['error']], 500);
        }

        // Create subscription using the plan
        $subResult = $this->razorpay->createSubscription($planResult['id']);

        if (isset($subResult['error'])) {
            return response()->json(['error' => $subResult['error']], 500);
        }

        // Save subscription in database
        Subscription::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'term' => $request->term,
            'razorpay_subscription_id' => $subResult['id'],
            'razorpay_plan_id' => $planResult['id'],
            'status' => 'active',
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'subscription_id' => $subResult['id'],
            'plan_id' => $planResult['id'],
        ]);
    }

    /**
     * Verify subscription payment.
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

        $sub = Subscription::where('razorpay_subscription_id', $request->razorpay_subscription_id)->first();
        if ($sub) {
            $sub->update(['status' => 'active']);

            // Send subscription confirmation email
            try {
                Mail::to($sub->user->email)->send(new SubscriptionConfirmation($sub));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send subscription email', ['error' => $e->getMessage()]);
            }
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
                    $this->issueLicenseKey($order);
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
