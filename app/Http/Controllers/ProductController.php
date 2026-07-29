<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\PackingQuantityOption;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load('variants.vendor', 'category', 'vendors');
        $usedOptionIds = $product->vendors
            ->pluck('pivot.packing_quantity_option_id')
            ->filter()
            ->merge($product->variants->pluck('packing_quantity_option_id')->filter())
            ->unique()
            ->values();

        $packingQuantityOptions = PackingQuantityOption::query()
            ->where('is_active', true)
            ->orWhereIn('id', $usedOptionIds)
            ->get()
            ->keyBy('id');
        return view('product.show', compact('product', 'packingQuantityOptions'));
    }
}