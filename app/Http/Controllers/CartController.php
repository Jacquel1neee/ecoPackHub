<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
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
        $items = $cart->items()->with('product')->get();
        $total = $items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        
        return view('cart.index', compact('items', 'total'));
    }

    // Add product to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:999'
        ]);

        $cart = $this->getCart();
        $product = Product::find($request->product_id);
        $quantity = $request->quantity ?? 1;

        // Check if product already in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
        }

        return redirect()->route('cart.index')
            ->with('success', "{$product->name} added to cart!");
    }

    // Update cart item quantity
    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        // Ensure item belongs to user's cart
        if ($item->cart->user_id !== Auth::id()) {
            abort(403);
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