<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderItemSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_items_can_be_created_with_variant_id_without_product_id(): void
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'code' => 'TEST-001',
            'name' => 'Test Product',
            'description' => 'Sample',
            'material' => null,
            'image' => null,
            'image_path' => null,
            'product_group' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $variantId = DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'size' => 'M',
            'packing_quantity' => '1',
            'price' => 10.00,
            'stock' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => 'ECO-TEST',
            'total_amount' => 10.00,
            'status' => 'pending',
            'shipping_address' => 'Test address',
            'phone' => '0123456789',
            'notes' => null,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $variantId,
            'quantity' => 1,
            'price' => 10.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'variant_id' => $variantId,
        ]);
    }
}
