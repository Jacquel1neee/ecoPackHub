<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Display the home page with categories and products.
     */
    public function index()
    {
        // Get all categories with product count
        $categories = Category::withCount('products')->get();
        
        // Get all products with their category relationship
        $products = Product::with('category')->get();

        // Pass data to the view
        return view('home', compact('categories', 'products'));
    }
}