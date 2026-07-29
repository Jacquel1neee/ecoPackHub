<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PackingQuantityOption;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VendorProductAssignmentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function vendor_updates_set_product_pricing_without_preferred_assignment(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $packingQuantityOption = PackingQuantityOption::create([
            'name' => 'pcs/ctn',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-001',
            'name' => 'Paper Cup',
            'description' => 'Test product',
            'material' => 'Paper',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'size' => 'Standard',
            'packing_quantity' => '100 pcs',
            'packing_quantity_option_id' => null,
            'price' => 15.00,
            'stock' => 20,
            'vendor_id' => null,
            'vendor_price' => null,
        ]);

        $vendorA = Vendor::create([
            'name' => 'Vendor A',
            'is_active' => true,
        ]);

        $vendorB = Vendor::create([
            'name' => 'Vendor B',
            'is_active' => true,
        ]);

        $vendorB->products()->attach($product->id, [
            'price' => 12.00,
            'quantity' => 120,
            'is_preferred' => false,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.vendors.update', $vendorA), [
                'name' => $vendorA->name,
                'contact_person' => null,
                'email' => null,
                'phone' => null,
                'address' => null,
                'is_active' => 1,
                'products' => [
                    $product->id => [
                        'selected' => 1,
                        'quantity' => 150,
                        'packing_quantity_option_id' => $packingQuantityOption->id,
                        'price' => 10.50,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.vendors.index'));

        $this->assertDatabaseHas('product_vendor', [
            'product_id' => $product->id,
            'vendor_id' => $vendorA->id,
            'price' => 10.50,
            'quantity' => 150,
            'packing_quantity_option_id' => $packingQuantityOption->id,
        ]);

        $this->assertDatabaseHas('product_vendor', [
            'product_id' => $product->id,
            'vendor_id' => $vendorB->id,
            'price' => 12.00,
            'quantity' => 120,
        ]);
    }

    #[Test]
    public function admin_can_choose_a_vendor_for_a_product_variant_from_the_product_form(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);

        $vendor = Vendor::create([
            'name' => 'Chosen Vendor',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-002',
            'name' => 'Lunch Box',
            'description' => 'Product for vendor selection',
            'material' => 'Paper',
        ]);

        $product->vendors()->attach($vendor->id, [
            'price' => 8.75,
            'is_preferred' => 0,
        ]);

        $packingQuantityOption = PackingQuantityOption::create([
            'name' => '50 pcs',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $category->id,
                'code' => $product->code,
                'name' => $product->name,
                'description' => $product->description,
                'material' => $product->material,
                'discount_price' => null,
                'discount_percentage' => null,
                'is_discount_active' => 0,
                'variants' => [
                    0 => [
                        'size' => 'Standard',
                        'packing_quantity' => '50 pcs',
                        'packing_quantity_option_id' => $packingQuantityOption->id,
                        'price' => 14.50,
                        'stock' => 30,
                        'vendor_id' => $vendor->id,
                        'vendor_price' => 8.75,
                    ],
                ],
            ])
            ->assertRedirect(route('admin.products.index'));

        $variant = $product->fresh()->variants()->first();

        $this->assertNotNull($variant);
        $this->assertSame($vendor->id, $variant->vendor_id);
        $this->assertSame('8.75', (string) $variant->vendor_price);
        $this->assertSame($packingQuantityOption->id, $variant->packing_quantity_option_id);
        $this->assertSame('50 pcs', $variant->packing_quantity);
        $this->assertDatabaseHas('product_vendor', [
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'price' => 8.75,
        ]);
    }
}
