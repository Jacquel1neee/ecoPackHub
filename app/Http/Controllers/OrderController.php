<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // Checkout page
    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $items = $cart->items()->with('variant.product')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->variant->price ?? 0);
        });

        return view('order.checkout', compact('items', 'total'));
    }

    // Place order
    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        // 使用 variant 关系计算 total
        $items = $cart->items()->with('variant')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->variant->price ?? 0);
        });

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ECO-' . Str::upper(Str::random(8)),
            'total_amount' => $total,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'phone' => $request->phone,
            'notes' => $request->notes
        ]);

        // Create order items
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $item->variant->price ?? 0
            ]);
        }

        // Clear cart
        $cart->items()->delete();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Order #: ' . $order->order_number);
    }

    // View all user orders
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('order.index', compact('orders'));
    }

    // View single order
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 1) {
            abort(403);
        }

        $order->load('items.variant.product');
        return view('order.show', compact('order'));
    }
}