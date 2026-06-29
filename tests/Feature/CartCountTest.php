<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCountTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_cart_count_without_ajax_header(): void
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'icon' => 'box',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'TEST-001',
            'name' => 'Test Product',
            'description' => 'Test description',
            'packing_quantity' => '10',
            'material' => 'Paper',
            'size' => 'M',
            'price' => 12.50,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => 'M',
            'packing_quantity' => '10',
            'price' => 12.50,
            'stock' => 20,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user)->getJson('/cart/count');

        $response->assertOk()
            ->assertJsonPath('count', 3);
    }
}
