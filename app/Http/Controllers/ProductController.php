<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load('variants', 'category');
        return view('product.show', compact('product'));
    }
}