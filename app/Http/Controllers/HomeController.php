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
        // Get search term from request
        $searchTerm = $request->input('search');

        // Get all categories with product count
        $categories = Category::withCount('products')->get();

        // ===== SEARCH FUNCTIONALITY =====
        if ($searchTerm) {
            // Search products
            $query = Product::with('category', 'variants')
                ->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                ->orWhere('material', 'LIKE', "%{$searchTerm}%");

            $products = $query->get();

            // Get all products for filters (unfiltered)
            $allProducts = Product::with('variants')->get();

            // Get categories for sidebar (with products count)
            $categories = Category::withCount('products')->get();
        } else {
            // No search: get all products
            $query = Product::with('category', 'variants');
            $products = $query->get();
            $allProducts = Product::with('variants')->get();
        }

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