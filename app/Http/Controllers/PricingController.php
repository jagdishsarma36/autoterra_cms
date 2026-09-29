<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PricingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currency = $request->get('currency', 'INR');

        $products = Product::with('prices')->where('is_active', true)->orderBy('sort_order')->get();

        $data = [];
        foreach ($products as $product) {
            $prices = [];
            foreach ($product->prices as $price) {
                if (!$price->is_active) continue;

                $amount = $currency === 'INR' ? $price->price_inr : $price->price_usd;
                $cycle = CartService::billingCycle($price->term);

                $prices[$price->term] = [
                    'amount' => $amount,
                    'cycles' => $price->billing_cycles,
                    // What one recurring charge costs. Derived from the same
                    // pricing the server bills, so the buy page cannot quote a
                    // per-cycle figure the checkout would then contradict.
                    'cycle_base' => $amount === null
                        ? null
                        : CartService::getCycleBase((float) $amount, $price->term),
                    'cycle_amount' => $amount === null
                        ? null
                        : CartService::getCycleAmount((float) $amount, $price->term, $currency),
                    'cycle_count' => $cycle['cycles'],
                    'cycle_period' => $cycle['period'],
                    'cycle_interval' => $cycle['interval'],
                ];
            }
            $data[$product->slug] = [
                'sku' => $product->sku,
                'name' => $product->name,
                'tier' => $product->tier,
                'prices' => $prices,
            ];
        }

        return response()->json([
            'currency' => $currency,
            'products' => $data,
        ]);
    }
}
