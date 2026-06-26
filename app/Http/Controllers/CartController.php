<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Get or create cart for current user
    private function getCart()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart) {
            $cart = Cart::create(['user_id' => Auth::id()]);
        }
        return $cart;
    }

    // View cart page
    public function index()
    {
        $cart = $this->getCart();
        $items = $cart->items()->with('variant.product')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * $item->variant->price;
        });
        
        return view('cart.index', compact('items', 'total'));
    }

    // ===== AJAX: Get cart count =====
    public function getCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        
        $cart = Cart::where('user_id', Auth::id())->first();
        if (!$cart) {
            return response()->json(['count' => 0]);
        }
        
        $count = $cart->items()->sum('quantity');
        return response()->json(['count' => $count]);
    }

    // ===== AJAX: Add product variant to cart (returns JSON) =====
    public function add(Request $request)
    {
        try {
            $request->validate([
                'variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'integer|min:1|max:999'
            ]);

            $cart = $this->getCart();
            $variant = ProductVariant::with('product')->find($request->variant_id);
            $quantity = $request->quantity ?? 1;

            // Check stock
            if ($variant->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available. Only ' . $variant->stock . ' left.'
                ], 400);
            }

            // Check if variant already in cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($variant->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Not enough stock. You already have ' . $cartItem->quantity . ' in cart.'
                    ], 400);
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'variant_id' => $variant->id,
                    'quantity' => $quantity
                ]);
            }

            // Get updated cart count
            $count = $cart->items()->sum('quantity');

            $variantName = $variant->size ? $variant->product->name . ' (' . $variant->size . ')' : $variant->product->name;

            return response()->json([
                'success' => true,
                'message' => "{$variantName} added to cart!",
                'cart_count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update cart item quantity
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        // Check stock
        if ($item->variant->stock < $request->quantity) {
            return redirect()->route('cart.index')
                ->with('error', 'Not enough stock available. Only ' . $item->variant->stock . ' left.');
        }

        $item->quantity = $request->quantity;
        $item->save();

        return redirect()->route('cart.index')
            ->with('success', 'Cart updated!');
    }

    // Remove item from cart
    public function remove(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Item removed from cart!');
    }

    // Clear entire cart
    public function clear()
    {
        $cart = $this->getCart();
        $cart->items()->delete();

        return redirect()->route('cart.index')
            ->with('success', 'Cart cleared!');
    }
}