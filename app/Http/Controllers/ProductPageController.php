<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductPageController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Get all products (for AI suggestion)
        $allProducts = Product::with(['variants', 'vendors'])->get();

        // ===== SEARCH FUNCTIONALITY =====
        if ($searchTerm) {
            // Search products
            $categories = Category::with(['products' => function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('material', 'LIKE', "%{$searchTerm}%")
                    ->with(['variants', 'vendors']);
            }])->get();

            // Only keep categories with products
            $categories = $categories->filter(function($category) {
                return $category->products->count() > 0;
            });

            // Get all products (for AI suggestion, unfiltered)
            $allProducts = Product::with('variants')->get();
        } else {
            // No search: get all categories with products
            $categories = Category::with(['products' => function($query) {
                $query->with(['variants', 'vendors']);
            }])->get();

            // Get all products for AI suggestion
            $allProducts = Product::with(['variants', 'vendors'])->get();
        }

        return view('products.index', compact('categories', 'allProducts', 'searchTerm'));
    }
}