<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalRevenue = Sale::sum('total_revenue') ?? 0;
        $totalSales = Sale::sum('quantity_sold') ?? 0;

        $salesData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySales = Sale::whereDate('sale_date', $date)->sum('total_revenue');
            $salesData[] = [
                'date' => $date->format('d M'),
                'revenue' => (float) $dailySales,
            ];
        }

        $topProducts = Product::withSum('sales', 'quantity_sold')
            ->orderBy('sales_sum_quantity_sold', 'desc')
            ->limit(5)
            ->get();

        $recentProducts = Product::with('category')
            ->latest()
            ->limit(10)
            ->get();

        $products = Product::with('category')->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalRevenue',
            'totalSales',
            'salesData',
            'topProducts',
            'recentProducts',
            'products'
        ));
    }
}