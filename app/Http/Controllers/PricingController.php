<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
                $prices[$price->term] = [
                    'amount' => $currency === 'INR' ? $price->price_inr : $price->price_usd,
                    'cycles' => $price->billing_cycles,
                ];
            }
            $data[$product->slug] = [
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
