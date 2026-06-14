<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\LicenseKey;
use App\Models\Product;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeSubscriptions = $user->subscriptions()->where('status', 'active')->with('product')->get();
        $activeLicenses = $user->licenseKeys()->where('is_active', true)->with('product')->get();
        $recentOrders = $user->orders()->with('product')->latest()->take(5)->get();

        return view('dashboard.index', compact('activeSubscriptions', 'activeLicenses', 'recentOrders'));
    }

    public function subscriptions()
    {
        $subscriptions = Auth::user()->subscriptions()->with('product')->latest()->get();
        return view('dashboard.subscriptions', compact('subscriptions'));
    }

    public function subscriptionDetail(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }
        $subscription->load('product');

        $invoices = [];
        $autoCancelled = false;

        if ($subscription->razorpay_subscription_id) {
            $razorpay = app(RazorpayService::class);
            $result = $razorpay->fetchInvoices($subscription->razorpay_subscription_id);

            if (!isset($result['error'])) {
                $invoices = $result['items'] ?? [];
            }

            // Auto-cancel logic: if any invoice is pending/created for 7+ days
            if ($subscription->status === 'active') {
                $pendingOld = collect($invoices)->first(function ($inv) {
                    if (($inv['status'] ?? '') !== 'created') return false;
                    $createdAt = $inv['created_at'] ?? null;
                    if (!$createdAt) return false;
                    $created = \Carbon\Carbon::createFromTimestamp($createdAt);
                    return $created->diffInDays(now()) >= 7;
                });

                if ($pendingOld) {
                    if ($subscription->razorpay_subscription_id) {
                        $razorpay->cancelSubscription($subscription->razorpay_subscription_id);
                    }
                    $subscription->update(['status' => 'cancelled']);
                    $autoCancelled = true;
                }
            }
        }

        return view('dashboard.subscription-detail', compact('subscription', 'invoices', 'autoCancelled'));
    }

    public function subscriptionPrint(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }
        $subscription->load(['product', 'user']);
        return view('dashboard.subscription-print', compact('subscription'));
    }

    public function cancelSubscription(Subscription $subscription)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $razorpay = app(\App\Services\RazorpayService::class);

        if ($subscription->razorpay_subscription_id) {
            $razorpay->cancelSubscription($subscription->razorpay_subscription_id);
        }

        $subscription->update(['status' => 'cancelled']);

        return back()->with('success', 'Subscription cancelled successfully.');
    }

    public function retryInvoicePayment(Subscription $subscription, Request $request)
    {
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'invoice_id' => 'required|string',
        ]);

        if (!$subscription->razorpay_subscription_id) {
            return response()->json(['error' => 'No Razorpay subscription found.'], 400);
        }

        $razorpay = app(RazorpayService::class);
        $invoice = $razorpay->fetchInvoice($request->invoice_id);

        if (isset($invoice['error'])) {
            return response()->json(['error' => 'Failed to fetch invoice: ' . $invoice['error']], 500);
        }

        if (($invoice['status'] ?? '') !== 'failed') {
            return response()->json(['error' => 'This invoice is not in a failed state.'], 400);
        }

        // Create a new order for the retry amount
        $amount = $invoice['amount'] ?? 0;
        if ($amount <= 0) {
            return response()->json(['error' => 'Invalid invoice amount.'], 400);
        }

        $order = $razorpay->createOrder(
            $amount,
            $invoice['currency'] ?? 'INR',
            [
                'subscription_id' => $subscription->razorpay_subscription_id,
                'invoice_id' => $request->invoice_id,
                'retry' => true,
            ]
        );

        if (isset($order['error'])) {
            return response()->json(['error' => 'Failed to create payment order: ' . $order['error']], 500);
        }

        // Create a local order for tracking
        $dbOrder = Order::create([
            'user_id' => Auth::id(),
            'product_id' => $subscription->product_id,
            'term' => $subscription->term,
            'currency' => $invoice['currency'] ?? 'INR',
            'amount' => $amount / (($invoice['currency'] ?? 'INR') === 'INR' ? 1 : 100),
            'gst_amount' => 0,
            'total_amount' => $amount / (($invoice['currency'] ?? 'INR') === 'INR' ? 1 : 100),
            'razorpay_order_id' => $order['id'],
            'status' => 'pending',
            'billing_mode' => 'subscription',
        ]);

        return response()->json([
            'success' => true,
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'db_order_id' => $dbOrder->id,
            'key_id' => $razorpay->getKeyId(),
            'product_name' => $subscription->product->name ?? 'Subscription',
            'user_name' => Auth::user()->name ?? '',
            'user_email' => Auth::user()->email ?? '',
        ]);
    }

    public function licenses()
    {
        $licenses = Auth::user()->licenseKeys()->with('product')->latest()->get();
        return view('dashboard.licenses', compact('licenses'));
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('product')->latest()->get();
        return view('dashboard.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['product', 'licenseKeys']);
        return view('dashboard.order-detail', compact('order'));
    }

    public function orderPrint(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        $order->load(['product', 'licenseKeys', 'user']);
        return view('dashboard.order-print', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($request->only(['name', 'phone', 'company', 'address']));

        return back()->with('success', 'Profile updated successfully.');
    }
}
