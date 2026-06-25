<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories with product count
        $categories = Category::withCount('products')->get();
        
        $allProducts = Product::all();
        
        $query = Product::with('category');

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by material (multiple values)
        if ($request->has('material') && $request->material != '') {
            $materials = explode(',', $request->material);
            $query->whereIn('material', $materials);
        }

        // Filter by size (multiple values)
        if ($request->has('size') && $request->size != '') {
            $sizes = explode(',', $request->size);
            $query->whereIn('size', $sizes);
        }

        // Filter by price range
        if ($request->has('price_min') && $request->price_min != '') {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max') && $request->price_max != '') {
            $query->where('price', '<=', $request->price_max);
        }

        // Get filtered products
        $products = $query->get();

        $totalProductsCount = Product::count();

        return view('home', compact(
            'categories', 
            'products', 
            'allProducts', 
            'totalProductsCount'
        ));
    }
}