<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Services\ToyyibPayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display checkout page
     */
    public function checkout()
    {
        // Get current user's cart
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        // Get cart items with variant and product details
        $items = $cart->items()->with('variant.product')->get();
        
        // Calculate subtotal
        $total = $items->sum(function ($item) {
            return $item->quantity * ($item->variant->price ?? 0);
        });

        // Return checkout view
        return view('order.checkout', compact('items', 'total'));
    }

    /**
     * Place order - process and save order
     */
    public function placeOrder(Request $request, ToyyibPayService $toyyibPayService)
    {
        // ===== 1. Validate request data =====
        $request->validate([
            'delivery_method' => 'required|in:shipping,selfpickup',
            'shipping_address' => 'required_if:delivery_method,shipping|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        // ===== 2. Get user's cart =====
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty!');
        }

        // ===== 3. Calculate subtotal =====
        $items = $cart->items()->with('variant')->get();
        $subtotal = $items->sum(function ($item) {
            return $item->quantity * ($item->variant->price ?? 0);
        });

        // ===== 4. Calculate shipping fee =====
        // Shipping: RM 5.00, Self Pickup: FREE (RM 0.00)
        $shippingFee = $request->delivery_method === 'shipping' ? 5.00 : 0.00;
        $total = $subtotal + $shippingFee;

        // ===== 5. Handle shipping address =====
        // If self pickup, auto-fill with store address; if shipping, use user's address
        $shippingAddress = $request->delivery_method === 'shipping'
            ? $request->shipping_address
            : 'Self Pickup - EcoPack Hub Store, 123 Jalan Example, 43000 Kajang, Selangor';

        // ===== 6. Create order =====
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ECO-' . Str::upper(Str::random(8)),
            'subtotal' => $subtotal,              // Subtotal before shipping
            'shipping_fee' => $shippingFee,       // Shipping fee amount
            'total_amount' => $total,             // Grand total
            'status' => 'pending',
            'payment_status' => 'pending',
            'delivery_method' => $request->delivery_method,
            'shipping_address' => $shippingAddress,
            'phone' => $request->phone,
            'notes' => $request->notes
        ]);

        // ===== 7. Create order items =====
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $item->variant->price ?? 0
            ]);
        }

        // ===== 8. Clear cart =====
        $cart->items()->delete();

        // ===== 8. Redirect to order details =====
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order placed successfully! Order #: ' . $order->order_number);
    }

    /**
     * Display list of user's orders
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('order.index', compact('orders'));
    }

    /**
     * Display single order details
     */
    public function show(Order $order)
    {
        // Authorization: user can only view their own orders, or if admin (role = 1)
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 1) {
            abort(403);
        }

        $order->load('items.variant.product');
        return view('order.show', compact('order'));
    }

    public function pay(Order $order, ToyyibPayService $toyyibPayService)
    {
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 1) {
            abort(403);
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('orders.show', ['order' => $order->id])
                ->with('info', 'This order has already been paid.');
        }

        $paymentResult = $toyyibPayService->createBill($order);

        if (($paymentResult['success'] ?? false) && ! empty($paymentResult['redirect_url'])) {
            $message = ($paymentResult['mode'] ?? 'real') === 'real'
                ? 'You will be redirected to ToyyibPay to complete your payment.'
                : 'Payment flow started with the local fallback.';

            return redirect($paymentResult['redirect_url'])
                ->with('success', $message);
        }

        return redirect()->route('orders.show', ['order' => $order->id])
            ->with('error', $paymentResult['message'] ?? 'Unable to start ToyyibPay payment right now.');
    }

    public function completeMockPayment(Order $order)
    {
        if ($order->user_id !== Auth::id() && Auth::user()->role !== 1) {
            abort(403);
        }

        $order->update([
            'payment_status' => Order::PAYMENT_PAID,
            'status' => 'processing',
        ]);

        return redirect()->route('orders.show', ['order' => $order->id])
            ->with('success', 'Mock payment completed successfully.');
    }

    public function paymentReturn(Order $order, Request $request)
    {
        if ($this->isSuccessfulPayment($request)) {
            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
                'status' => 'processing',
            ]);

            return redirect()->route('orders.show', ['order' => $order->id])
                ->with('success', 'Payment completed successfully.');
        }

        $order->update(['payment_status' => Order::PAYMENT_FAILED]);

        return redirect()->route('orders.show', $order)
            ->with('error', 'Payment was not completed.');
    }

    public function paymentCallback(Request $request)
    {
        $orderNumber = $request->input('order_id')
            ?: $request->input('billExternalReferenceNo')
            ?: $request->input('order_number');

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();
            if ($order && $this->isSuccessfulPayment($request)) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'status' => 'processing',
                ]);
            }
        }

        return response()->json(['status' => 'OK']);
    }

    private function isSuccessfulPayment(Request $request): bool
    {
        $status = (string) $request->input('status_id', $request->input('status', ''));
        $normalized = strtolower(trim($status));

        return in_array($normalized, ['1', 'success', 'paid', 'completed', 'true'], true);
    }
}