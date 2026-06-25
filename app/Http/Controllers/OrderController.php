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
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Checkout page
    public function checkout()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        $items = $cart->items()->with('product')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * $item->product->price;
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

        // Calculate total
        $items = $cart->items()->with('product')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * $item->product->price;
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
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
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

        $order->load('items.product');
        return view('order.show', compact('order'));
    }

    // Admin: View all orders
    public function adminIndex()
    {
        if (Auth::user()->role !== 1) {
            abort(403);
        }

        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Admin: Update order status
    public function updateStatus(Request $request, Order $order)
    {
        if (Auth::user()->role !== 1) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$order->order_number} status updated to {$order->status_label}!");
    }
}