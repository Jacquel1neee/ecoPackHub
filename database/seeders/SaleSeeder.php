<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            for ($i = 0; $i < 30; $i++) {
                $date = now()->subDays($i);
                $quantity = rand(5, 100);
                $price = $product->price ?? rand(10, 50);
                
                Sale::create([
                    'product_id' => $product->id,
                    'quantity_sold' => $quantity,
                    'total_revenue' => $quantity * $price,
                    'sale_date' => $date,
                ]);
            }
        }
    }
}