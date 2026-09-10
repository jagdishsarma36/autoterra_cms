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
            'item_count' => $this->cart->getItemCount(),
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
            $request->integer('quantity', 1)
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
        ]);

        $this->cart->removeItem($request->product_slug, $request->term);

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
        ]);

        $this->cart->updateQuantity(
            $request->product_slug,
            $request->term,
            $request->integer('quantity')
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
            'contents' => $this->cart->getContents('INR'),
        ]);
    }

    public function removeCoupon(): JsonResponse
    {
        $this->cart->removeCoupon();

        return response()->json([
            'success' => true,
            'contents' => $this->cart->getContents('INR'),
        ]);
    }
}
