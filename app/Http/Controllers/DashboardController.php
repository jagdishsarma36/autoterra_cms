<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Subscription;
use App\Models\LicenseKey;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('dashboard.subscription-detail', compact('subscription'));
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
