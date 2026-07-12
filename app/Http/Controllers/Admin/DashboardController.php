<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Sale;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Stats =====
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalOrders = Order::count();

        // ===== Revenue =====
        $totalRevenue = Sale::sum('total_revenue') ?? 0;
        $totalSales = Sale::sum('quantity_sold') ?? 0;

        // ===== Sales Data for Chart (Last 30 Days) =====
        $salesData = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_revenue) as revenue')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== Top Products =====
        $topProducts = Product::withSum('sales', 'quantity_sold')
            ->orderBy('sales_sum_quantity_sold', 'desc')
            ->limit(5)
            ->get();

        // ===== Recent Products with Vendors =====
        $recentProducts = Product::with(['category', 'variants', 'vendors'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ===== Vendor Summary =====
        $vendorSummary = Vendor::withCount('products')
            ->with(['products' => function($query) {
                $query->select('products.id', 'product_vendor.price');
            }])
            ->get()
            ->map(function($vendor) {
                $vendor->total_value = $vendor->products->sum(function($product) {
                    return $product->pivot->price ?? 0;
                });
                return $vendor;
            });

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalVendors',
            'activeVendors',
            'totalUsers',
            'totalOrders',
            'totalRevenue',
            'totalSales',
            'salesData',
            'topProducts',
            'recentProducts',
            'vendorSummary'
        ));
    }
}