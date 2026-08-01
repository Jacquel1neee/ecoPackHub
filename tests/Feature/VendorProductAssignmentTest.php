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

    #[Test]
    public function product_edit_quantifier_uses_the_per_variant_vendor_page_quantity(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $option = PackingQuantityOption::create([
            'name' => 'pcs/ctn',
            'is_active' => true,
        ]);
        $vendor = Vendor::create([
            'name' => 'Quantity Vendor',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-003',
            'name' => 'Food Container',
        ]);
        $product->vendors()->attach($vendor->id, [
            'price' => 9.50,
            'quantity' => 240,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'size' => 'Standard',
            'packing_quantity' => '100 pcs',
            'packing_quantity_option_id' => $option->id,
            'price' => 15.00,
            'vendor_price' => 9.50,
            'vendor_quantity' => 100,
            'stock' => 20,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('value="100 pcs/ctn"', false)
            ->assertSee('data-quantity="100"', false);
    }

    #[Test]
    public function product_edit_quantifier_keeps_the_legacy_quantity_when_vendor_quantity_is_not_saved(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $option = PackingQuantityOption::create([
            'name' => 'pcs/ctn',
            'is_active' => true,
        ]);
        $vendor = Vendor::create([
            'name' => 'Legacy Quantity Vendor',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-004',
            'name' => 'Legacy Container',
        ]);
        $product->vendors()->attach($vendor->id, [
            'price' => 9.50,
            'quantity' => null,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'size' => 'Standard',
            'packing_quantity' => '1000 pcs/ctn',
            'packing_quantity_option_id' => $option->id,
            'price' => 15.00,
            'vendor_price' => 9.50,
            'vendor_quantity' => null,
            'stock' => 20,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('value="1000 pcs/ctn"', false)
            ->assertSee('class="variant-vendor-quantity" value="1000"', false)
            ->assertSee("option?.dataset.quantity || vendorQuantityInput?.value || ''", false);
    }

    #[Test]
    public function product_edit_quantifier_uses_vendor_assignment_when_variant_vendor_fields_are_empty(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $option = PackingQuantityOption::create([
            'name' => 'pcs/ctn',
            'is_active' => true,
        ]);
        $vendor = Vendor::create([
            'name' => 'Mapped Vendor',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-005',
            'name' => 'Mapped Container',
        ]);

        $product->vendors()->attach($vendor->id, [
            'price' => 9.50,
            'quantity' => 240,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => null,
            'size' => 'Standard',
            'packing_quantity' => $option->name,
            'packing_quantity_option_id' => null,
            'price' => 15.00,
            'vendor_price' => null,
            'vendor_quantity' => null,
            'stock' => 20,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.edit', $product))
            ->assertOk()
            ->assertSee('value="240 pcs/ctn"', false)
            ->assertSee('data-quantity="240"', false)
            ->assertSee('data-option-name="pcs/ctn"', false)
            ->assertDontSee("option?.dataset.price || priceInput.value || ''", false);
    }

    #[Test]
    public function product_edit_quantifier_shows_quantity_for_selected_vendor_even_if_current_variant_row_is_missing_it(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $option = PackingQuantityOption::create([
            'name' => 'pcs/ctn',
            'is_active' => true,
        ]);
        $vendor = Vendor::create([
            'name' => 'Quantity Carry Vendor',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-006',
            'name' => 'Carry Quantity Box',
        ]);

        $product->vendors()->attach($vendor->id, [
            'price' => 9.50,
            'quantity' => 240,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'size' => 'Small',
            'packing_quantity' => $option->name,
            'packing_quantity_option_id' => $option->id,
            'price' => 13.00,
            'vendor_price' => 8.50,
            'vendor_quantity' => 240,
            'stock' => 10,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => null,
            'size' => 'Large',
            'packing_quantity' => $option->name,
            'packing_quantity_option_id' => $option->id,
            'price' => 15.00,
            'vendor_price' => null,
            'vendor_quantity' => null,
            'stock' => 20,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

        $response
            ->assertOk()
            ->assertSee('data-quantity="240"', false)
            ->assertSee('data-option-name="pcs/ctn"', false);

        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertGreaterThanOrEqual(2, substr_count((string) $content, 'value="240 pcs/ctn"'));
    }

    #[Test]
    public function product_edit_quantifier_falls_back_to_stock_when_vendor_quantity_is_missing_everywhere(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 1]);
        $category = Category::create([
            'name' => 'Packaging',
            'slug' => Str::slug('Packaging'),
        ]);
        $option = PackingQuantityOption::create([
            'name' => 'pcs/pkt',
            'is_active' => true,
        ]);
        $vendorA = Vendor::create([
            'name' => 'ABC.sdn.bhd',
            'is_active' => true,
        ]);
        $vendorB = Vendor::create([
            'name' => 'gallery taste',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'code' => 'P-007',
            'name' => 'Paper Tray (Big Tree)',
        ]);

        $product->vendors()->attach($vendorA->id, [
            'price' => 45.00,
            'quantity' => null,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);
        $product->vendors()->attach($vendorB->id, [
            'price' => 12.00,
            'quantity' => null,
            'packing_quantity_option_id' => $option->id,
            'is_preferred' => false,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendorA->id,
            'size' => 'Pro',
            'packing_quantity' => 'pcs/pkt',
            'packing_quantity_option_id' => $option->id,
            'price' => 55.00,
            'vendor_price' => 45.00,
            'vendor_quantity' => null,
            'stock' => 2000,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendorA->id,
            'size' => 'Standard',
            'packing_quantity' => 'pcs/pkt',
            'packing_quantity_option_id' => $option->id,
            'price' => 50.00,
            'vendor_price' => 45.00,
            'vendor_quantity' => null,
            'stock' => 1000,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'vendor_id' => $vendorB->id,
            'size' => 'basic',
            'packing_quantity' => 'pcs/pkt',
            'packing_quantity_option_id' => $option->id,
            'price' => 45.00,
            'vendor_price' => 12.00,
            'vendor_quantity' => null,
            'stock' => 500,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

        $response
            ->assertOk()
            ->assertSee('value="2000 pcs/pkt"', false)
            ->assertSee('value="1000 pcs/pkt"', false)
            ->assertSee('value="500 pcs/pkt"', false);
    }
}
