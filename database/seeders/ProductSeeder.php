<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['slug' => 'view', 'name' => 'AutoTerra View', 'description' => 'View and share point cloud & survey files — no processing tools', 'tier' => 'basic', 'sort_order' => 1],
            ['slug' => 'lt', 'name' => 'AutoTerra LT', 'description' => 'Essential survey tools for individual practitioners', 'tier' => 'basic', 'sort_order' => 2],
            ['slug' => 'std', 'name' => 'AutoTerra Standard', 'description' => 'Complete survey, CAD & terrain for professional teams', 'tier' => 'pro', 'sort_order' => 3],
            ['slug' => 'spatial', 'name' => 'AutoTerra Spatial', 'description' => 'Standard + LiDAR point cloud processing', 'tier' => 'pro', 'sort_order' => 4],
            ['slug' => 'pro', 'name' => 'AutoTerra Pro', 'description' => 'Advanced terrain, DTM, cross-sections & engineering analysis', 'tier' => 'advanced', 'sort_order' => 5],
            ['slug' => 'prospatial', 'name' => 'AutoTerra Pro Spatial', 'description' => 'Complete platform — survey + LiDAR + terrain + roads', 'tier' => 'advanced', 'sort_order' => 6],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}
