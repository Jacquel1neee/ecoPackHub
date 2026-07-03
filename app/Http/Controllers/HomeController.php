<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get search term and filters from request
        $searchTerm = $request->input('search');
        $categorySlug = $request->query('category');
        $materialFilters = $request->query('material') ? array_filter(explode(',', $request->query('material'))) : [];
        $sizeFilters = $request->query('size') ? array_filter(explode(',', $request->query('size'))) : [];
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');

        // Get all categories with product count
        $categories = Category::withCount('products')->get();

        // Build product query with filters
        $query = Product::with('category', 'variants');

        if ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('material', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($categorySlug) {
            $query->whereHas('category', function ($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            });
        }

        if (!empty($materialFilters)) {
            $query->whereIn('material', $materialFilters);
        }

        if (!empty($sizeFilters)) {
            $query->whereHas('variants', function ($query) use ($sizeFilters) {
                $query->whereIn('size', $sizeFilters);
            });
        }

        if ($priceMin) {
            $query->whereHas('variants', function ($query) use ($priceMin) {
                $query->where('price', '>=', $priceMin);
            });
        }

        if ($priceMax) {
            $query->whereHas('variants', function ($query) use ($priceMax) {
                $query->where('price', '<=', $priceMax);
            });
        }

        $products = $query->get();
        $allProducts = Product::with('variants')->get();

        // Calculate total products count for "All Products" badge
        $totalProductsCount = Product::count();

        $cartCount = 0;
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
        }

        return view('home', compact(
            'categories',
            'products',
            'allProducts',
            'totalProductsCount',
            'searchTerm',
            'cartCount'
        ));
    }
}