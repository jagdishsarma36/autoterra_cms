<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    public function run(): void
    {
        // billing_cycles: how many times to charge for this term
        // daily=365, weekly=52, 3mo=3, 6mo=6, 1yr=12(monthly), 3yr=36, 5yr=60
        $pricing = [
            'view' => [
                'daily' => null, 'weekly' => null,
                '3mo' => null, '6mo' => null,
                '1yr'  => ['inr' => 4000,  'usd' => 59,  'cycles' => 12],
                '3yr'  => ['inr' => 8000,  'usd' => 118, 'cycles' => 36],
                '5yr'  => ['inr' => 12500, 'usd' => 185, 'cycles' => 60],
            ],
            'lt' => [
                'daily' => null, 'weekly' => null,
                '3mo' => null, '6mo' => null,
                '1yr'  => ['inr' => 9500,  'usd' => 139, 'cycles' => 12],
                '3yr'  => ['inr' => 20000, 'usd' => 294, 'cycles' => 36],
                '5yr'  => ['inr' => 30000, 'usd' => 441, 'cycles' => 60],
            ],
            'std' => [
                'daily' => ['inr' => 200,  'usd' => 3,   'cycles' => 30],
                'weekly' => ['inr' => 900, 'usd' => 13,  'cycles' => 4],
                '3mo' => ['inr' => 6000,  'usd' => 88,  'cycles' => 3],
                '6mo' => ['inr' => 9500,  'usd' => 139, 'cycles' => 6],
                '1yr'  => ['inr' => 15000, 'usd' => 220, 'cycles' => 12],
                '3yr'  => ['inr' => 33000, 'usd' => 485, 'cycles' => 36],
                '5yr'  => ['inr' => 47000, 'usd' => 691, 'cycles' => 60],
            ],
            'spatial' => [
                'daily' => ['inr' => 300,  'usd' => 5,   'cycles' => 30],
                'weekly' => ['inr' => 1400, 'usd' => 21,  'cycles' => 4],
                '3mo' => ['inr' => 9000,  'usd' => 132, 'cycles' => 3],
                '6mo' => ['inr' => 14000, 'usd' => 206, 'cycles' => 6],
                '1yr'  => ['inr' => 23000, 'usd' => 338, 'cycles' => 12],
                '3yr'  => ['inr' => 50000, 'usd' => 735, 'cycles' => 36],
                '5yr'  => ['inr' => 75000, 'usd' => 1102, 'cycles' => 60],
            ],
            'pro' => [
                'daily' => ['inr' => 550,  'usd' => 8,   'cycles' => 30],
                'weekly' => ['inr' => 2700, 'usd' => 40,  'cycles' => 4],
                '3mo' => ['inr' => 16500, 'usd' => 242, 'cycles' => 3],
                '6mo' => ['inr' => 28000, 'usd' => 411, 'cycles' => 6],
                '1yr'  => ['inr' => 45000, 'usd' => 661, 'cycles' => 12],
                '3yr'  => ['inr' => 100000, 'usd' => 1470, 'cycles' => 36],
                '5yr'  => ['inr' => 144000, 'usd' => 2117, 'cycles' => 60],
            ],
            'prospatial' => [
                'daily' => ['inr' => 850,  'usd' => 12,  'cycles' => 30],
                'weekly' => ['inr' => 4200, 'usd' => 62,  'cycles' => 4],
                '3mo' => ['inr' => 25000, 'usd' => 367, 'cycles' => 3],
                '6mo' => ['inr' => 40000, 'usd' => 588, 'cycles' => 6],
                '1yr'  => ['inr' => 60000, 'usd' => 882, 'cycles' => 12],
                '3yr'  => ['inr' => 131000, 'usd' => 1926, 'cycles' => 36],
                '5yr'  => ['inr' => 188000, 'usd' => 2764, 'cycles' => 60],
            ],
        ];

        foreach ($pricing as $slug => $terms) {
            $product = Product::where('slug', $slug)->first();
            if (!$product) continue;

            foreach ($terms as $term => $prices) {
                if ($prices === null) continue;

                ProductPrice::updateOrCreate(
                    ['product_id' => $product->id, 'term' => $term],
                    [
                        'price_inr' => $prices['inr'],
                        'price_usd' => $prices['usd'],
                        'is_active' => true,
                        'billing_cycles' => $prices['cycles'],
                    ]
                );
            }
        }
    }
}
