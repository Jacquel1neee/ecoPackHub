<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders for admin.
     */
    public function index()
    {
        if (Auth::user()->role !== 1) {
            abort(403, 'Unauthorized access.');
        }

        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (Auth::user()->role !== 1) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$order->order_number} status updated to " . ucfirst($order->status) . "!");
    }
}