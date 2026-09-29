<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    public function index(): JsonResponse
    {
        $currency = request('currency', 'INR');
        $contents = $this->cart->getContents($currency);

        return response()->json([
            'items' => $contents['items'],
            'subtotal' => $contents['subtotal'],
            'gst' => $contents['gst'],
            'discount' => $contents['discount'],
            'total' => $contents['total'],
            'coupon_code' => $contents['coupon_code'],
            'coupon_on_upfront_only' => $contents['coupon_on_upfront_only'],
            'upfront_subtotal' => $contents['upfront_subtotal'],
            'item_count' => $this->cart->getItemCount(),
            'billing_mode' => $contents['billing_mode'],
            'has_subscriptions' => $contents['has_subscriptions'],
            'has_upfront' => $contents['has_upfront'],
            'is_mixed' => $contents['is_mixed'],
            'subscription_count' => $contents['subscription_count'],
            'upfront_count' => $contents['upfront_count'],
        ]);
    }

    public function showCartPage()
    {
        $contents = $this->cart->getContents('INR');

        return view('pages.cart', [
            'contents' => $contents,
            'item_count' => $this->cart->getItemCount(),
            'coupon_code' => $contents['coupon_code'],
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
            'quantity' => 'nullable|integer|min:1|max:10',
            'billing_mode' => 'nullable|string|in:' . CartService::MODE_UPFRONT . ',' . CartService::MODE_SUBSCRIPTION,
        ]);

        $price = $this->cart->getItemPrice(
            $request->product_slug,
            $request->term,
            'INR'
        );

        if ($price === null) {
            return response()->json(['error' => 'Product or term not available.'], 404);
        }

        $this->cart->addItem(
            $request->product_slug,
            $request->term,
            $request->integer('quantity', 1),
            $request->input('billing_mode', CartService::MODE_UPFRONT)
        );

        return response()->json([
            'success' => true,
            'item_count' => $this->cart->getItemCount(),
            'contents' => $this->cart->getContents('INR'),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
            'billing_mode' => 'nullable|string|in:' . CartService::MODE_UPFRONT . ',' . CartService::MODE_SUBSCRIPTION,
        ]);

        $this->cart->removeItem(
            $request->product_slug,
            $request->term,
            $request->input('billing_mode')
        );

        return response()->json([
            'success' => true,
            'item_count' => $this->cart->getItemCount(),
            'contents' => $this->cart->getContents('INR'),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_slug' => 'required|string',
            'term' => 'required|string',
            'quantity' => 'required|integer|min:0|max:10',
            'billing_mode' => 'nullable|string|in:' . CartService::MODE_UPFRONT . ',' . CartService::MODE_SUBSCRIPTION,
        ]);

        $this->cart->updateQuantity(
            $request->product_slug,
            $request->term,
            $request->integer('quantity'),
            $request->input('billing_mode')
        );

        return response()->json([
            'success' => true,
            'item_count' => $this->cart->getItemCount(),
            'contents' => $this->cart->getContents('INR'),
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $result = $this->cart->applyCoupon($request->code);

        if ($result !== true) {
            return response()->json(['error' => $result], 422);
        }

        return response()->json([
            'success' => true,
            'coupon_code' => $this->cart->getCouponCode(),
            'item_count' => $this->cart->getItemCount(),
            'contents' => $this->cart->getContents('INR'),
        ]);
    }

    public function removeCoupon(): JsonResponse
    {
        $this->cart->removeCoupon();

        return response()->json([
            'success' => true,
            'item_count' => $this->cart->getItemCount(),
            'contents' => $this->cart->getContents('INR'),
        ]);
    }
}
