<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\LicenseKey;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = config('razorpay.license_webhook_secret');
        if (!$secret) {
            return response()->json(['success' => false, 'error' => 'License webhook not configured'], 500);
        }

        $providedSecret = $request->header('X-License-Secret', '');
        if (!hash_equals($secret, $providedSecret)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'razorpay_order_id' => 'nullable|string',
            'order_id' => 'nullable|integer',
            'license_keys' => 'required|array|min:1',
            'license_keys.*.sku' => 'required|string',
            'license_keys.*.license_key' => 'required|string',
            'license_keys.*.expires_at' => 'nullable|date_format:Y-m-d',
            'license_keys.*.max_activations' => 'nullable|integer|min:1',
        ]);

        if (!$request->razorpay_order_id && !$request->order_id) {
            return response()->json(['success' => false, 'error' => 'Either razorpay_order_id or order_id is required'], 422);
        }

        // Find the order
        $order = null;
        if ($request->razorpay_order_id) {
            $order = Order::where('razorpay_order_id', $request->razorpay_order_id)->first();
        }
        if (!$order && $request->order_id) {
            $order = Order::where('id', $request->order_id)->first();
        }

        if (!$order) {
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        $created = [];
        $orderItems = $order->orderItems()->get();
        $isMultiItem = $orderItems->isNotEmpty();

        foreach ($request->license_keys as $keyData) {
            $sku = strtoupper(trim($keyData['sku']));
            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                continue;
            }

            // Check for existing license key (idempotent)
            $existing = LicenseKey::where('order_id', $order->id)
                ->where('product_id', $product->id)
                ->where('license_key', $keyData['license_key'])
                ->first();

            if ($existing) {
                $created[] = [
                    'sku' => $sku,
                    'license_key_id' => $existing->id,
                    'status' => 'already_exists',
                ];
                continue;
            }

            // Determine expiry
            $expiresAt = isset($keyData['expires_at']) ? $keyData['expires_at'] : null;
            if (!$expiresAt && $isMultiItem) {
                $orderItem = $orderItems->firstWhere('product_id', $product->id);
                if ($orderItem) {
                    $expiresAt = now()->addDays(termDays($orderItem->term))->format('Y-m-d');
                }
            } elseif (!$expiresAt) {
                $expiresAt = now()->addDays(termDays($order->term))->format('Y-m-d');
            }

            $license = LicenseKey::create([
                'user_id' => $order->user_id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'license_key' => $keyData['license_key'],
                'activated_at' => now(),
                'expires_at' => $expiresAt,
                'is_active' => true,
                'max_activations' => $keyData['max_activations'] ?? 1,
            ]);

            $created[] = [
                'sku' => $sku,
                'license_key_id' => $license->id,
                'status' => 'created',
            ];
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'licenses_created' => count(array_filter($created, fn($c) => $c['status'] === 'created')),
            'license_keys' => $created,
        ]);
    }
}
