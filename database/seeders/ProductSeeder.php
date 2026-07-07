<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ===== CORN STARCH PRODUCTS (Category: Container) =====
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-001',
                'name' => 'Corn Starch 2-Compartment Lunch Box',
                'description' => 'Biodegradable corn starch lunch box with 2 compartments for rice and side dishes',
                'material' => 'Corn Starch',
                'product_group' => 'lunch-box',
                'image' => 'HIT_CTR_001.png',
                'variants' => [
                    ['size' => '600ml', 'packing_quantity' => '400 pcs/ctn', 'price' => 45.00, 'stock' => 100],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-002',
                'name' => 'Corn Starch Sauce Cup',
                'description' => 'Small biodegradable sauce cup for dips and condiments',
                'material' => 'Corn Starch',
                'product_group' => 'sauce-cup',
                'image' => 'HIT_CTR_002.png',
                'variants' => [
                    ['size' => '4oz', 'packing_quantity' => '1000 pcs/ctn', 'price' => 30.00, 'stock' => 200],
                    ['size' => '2oz', 'packing_quantity' => '1000 pcs/ctn', 'price' => 25.00, 'stock' => 200],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-004',
                'name' => 'Corn Starch Bowl',
                'description' => 'Biodegradable bowl made from corn starch, suitable for various meals',
                'material' => 'Corn Starch',
                'product_group' => 'bowl',
                'image' => 'HIT_CTR_004.png',
                'variants' => [
                    ['size' => '280ml', 'packing_quantity' => '300 pcs/ctn', 'price' => 40.00, 'stock' => 150],
                    ['size' => '400ml', 'packing_quantity' => '300 pcs/ctn', 'price' => 48.00, 'stock' => 150],
                    ['size' => '450ml', 'packing_quantity' => '300 pcs/ctn', 'price' => 50.00, 'stock' => 150],
                    ['size' => '800ml', 'packing_quantity' => '300 pcs/ctn', 'price' => 55.00, 'stock' => 150],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-008',
                'name' => 'Corn Starch Multi-Compartment Lunch Box',
                'description' => 'Biodegradable lunch box with multiple compartments for meal separation',
                'material' => 'Corn Starch',
                'product_group' => 'multi-lunch-box',
                'image' => 'HIT_CTR_008.png',
                'variants' => [
                    ['size' => '3 Compartment', 'packing_quantity' => '200 pcs/ctn', 'price' => 58.00, 'stock' => 80],
                    ['size' => '4 Compartment', 'packing_quantity' => '200 pcs/ctn', 'price' => 60.00, 'stock' => 80],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-010',
                'name' => 'Corn Starch Sauce Container with Lid',
                'description' => 'Leak-proof sauce container with tight-seal lid',
                'material' => 'Corn Starch',
                'product_group' => 'sauce-container',
                'image' => 'HIT_CTR_010.png',
                'variants' => [
                    ['size' => 'Standard', 'packing_quantity' => '3000 pcs/ctn', 'price' => 65.00, 'stock' => 50],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-011',
                'name' => 'Corn Starch Lunch Box',
                'description' => 'Single compartment lunch box for takeaways',
                'material' => 'Corn Starch',
                'product_group' => 'single-lunch-box',
                'image' => 'HIT_CTR_011.png',
                'variants' => [
                    ['size' => '500ml', 'packing_quantity' => '300 pcs/ctn', 'price' => 42.00, 'stock' => 100],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-012',
                'name' => 'Oval Bio Bagasse Box with Bio Lid',
                'description' => 'Oval shaped bagasse box with biodegradable lid',
                'material' => 'Bagasse',
                'product_group' => 'bagasse-box',
                'image' => 'HIT_CTR_012.png',
                'variants' => [
                    ['size' => '1000ml', 'packing_quantity' => '500 pcs/ctn', 'price' => 70.00, 'stock' => 60],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-013',
                'name' => 'Brown Paper Bowl with Paper Lid',
                'description' => 'Brown kraft paper bowl with matching paper lid, fully compostable',
                'material' => 'Paper',
                'product_group' => 'paper-bowl',
                'image' => 'HIT_CTR_013.png',
                'variants' => [
                    ['size' => '8oz', 'packing_quantity' => '500 pcs/ctn', 'price' => 35.00, 'stock' => 120],
                    ['size' => '26oz', 'packing_quantity' => '500 pcs/ctn', 'price' => 50.00, 'stock' => 120],
                ]
            ],
            [
                'category_id' => 1, // Container
                'code' => 'HIT-CTR-015',
                'name' => 'Bio Plate (Corn Starch)',
                'description' => 'Biodegradable plate made from corn starch',
                'material' => 'Corn Starch',
                'product_group' => 'plate',
                'image' => 'HIT_CTR_015.png',
                'variants' => [
                    ['size' => '9inch', 'packing_quantity' => '1000 pcs/ctn', 'price' => 40.00, 'stock' => 100],
                    ['size' => '10inch', 'packing_quantity' => '500 pcs/ctn', 'price' => 45.00, 'stock' => 100],
                ]
            ],

            // ===== UTENSILS (Category: Utensil) =====
            [
                'category_id' => 2, // Utensil
                'code' => 'HIT-UTS-001',
                'name' => 'Wooden Stirrer',
                'description' => 'Natural wooden stirrer perfect for coffee and tea',
                'material' => 'Wood',
                'product_group' => 'stirrer',
                'image' => 'HIT_UTS_001.png',
                'variants' => [
                    ['size' => 'Standard', 'packing_quantity' => '10000 pcs/ctn', 'price' => 20.00, 'stock' => 50],
                ]
            ],
            [
                'category_id' => 2, // Utensil
                'code' => 'HIT-UTS-002',
                'name' => 'Corn Starch Cutlery',
                'description' => 'Biodegradable cutlery made from corn starch',
                'material' => 'Corn Starch',
                'product_group' => 'cutlery',
                'image' => 'HIT_UTS_002.png',
                'variants' => [
                    ['size' => 'Spoon', 'packing_quantity' => '1000 pcs/ctn', 'price' => 28.00, 'stock' => 150],
                    ['size' => 'Fork', 'packing_quantity' => '1000 pcs/ctn', 'price' => 28.00, 'stock' => 150],
                ]
            ],
            [
                'category_id' => 2, // Utensil
                'code' => 'HIT-UTS-004',
                'name' => 'Wooden Cutlery',
                'description' => 'Natural wooden cutlery, durable and eco-friendly',
                'material' => 'Wood',
                'product_group' => 'wooden-cutlery',
                'image' => 'HIT_UTS_004.png',
                'variants' => [
                    ['size' => 'Spoon', 'packing_quantity' => '2500 pcs/ctn', 'price' => 32.00, 'stock' => 100],
                    ['size' => 'Fork', 'packing_quantity' => '2500 pcs/ctn', 'price' => 32.00, 'stock' => 100],
                ]
            ],
            [
                'category_id' => 2, // Utensil
                'code' => 'HIT-UTS-006',
                'name' => '3-in-1 Cutlery Set',
                'description' => 'Complete cutlery set with spoon, fork and knife',
                'material' => 'Wood/Corn Starch',
                'product_group' => 'cutlery-set',
                'image' => 'HIT_UTS_006.png',
                'variants' => [
                    ['size' => 'Standard', 'packing_quantity' => '500 sets/ctn', 'price' => 55.00, 'stock' => 80],
                ]
            ],

            // ===== CARRY BAGS (Category: Carry Bag) =====
            [
                'category_id' => 3, // Carry Bag
                'code' => 'HIT-CB-001',
                'name' => 'Bio Singlet Bag',
                'description' => 'Biodegradable singlet bag for groceries and retail',
                'material' => 'Biodegradable Plastic',
                'product_group' => 'bio-singlet',
                'image' => 'HIT_CB_001.png',
                'variants' => [
                    ['size' => '12 x 12"', 'packing_quantity' => '50 pcs/pkt', 'price' => 8.00, 'stock' => 200],
                    ['size' => '15 x 15"', 'packing_quantity' => '50 pcs/pkt', 'price' => 10.00, 'stock' => 200],
                    ['size' => '17 x 19"', 'packing_quantity' => '40 pcs/pkt', 'price' => 12.00, 'stock' => 200],
                    ['size' => '18 x 22"', 'packing_quantity' => '40 pcs/pkt', 'price' => 14.00, 'stock' => 200],
                    ['size' => '22 x 24"', 'packing_quantity' => '40 pcs/pkt', 'price' => 16.00, 'stock' => 200],
                    ['size' => '24 x 30"', 'packing_quantity' => '40 pcs/pkt', 'price' => 20.00, 'stock' => 200],
                ]
            ],
            [
                'category_id' => 3, // Carry Bag
                'code' => 'HIT-CB-007',
                'name' => 'Paper Bag with Base (SOS)',
                'description' => 'Brown kraft paper bag with self-opening base',
                'material' => 'Kraft Paper',
                'product_group' => 'paper-bag-sos',
                'image' => 'HIT_CB_007.png',
                'variants' => [
                    ['size' => '127 x 240 x 77mm', 'packing_quantity' => '1200 pcs/ctn', 'price' => 75.00, 'stock' => 100],
                    ['size' => '147 x 273 x 92mm', 'packing_quantity' => '1200 pcs/ctn', 'price' => 85.00, 'stock' => 100],
                    ['size' => '154 x 314 x 100mm', 'packing_quantity' => '1000 pcs/ctn', 'price' => 95.00, 'stock' => 100],
                    ['size' => '178 x 330 x 112mm', 'packing_quantity' => '1000 pcs/ctn', 'price' => 110.00, 'stock' => 100],
                ]
            ],
            [
                'category_id' => 3, // Carry Bag
                'code' => 'HIT-CB-010',
                'name' => 'Paper Bag with Handle',
                'description' => 'Brown kraft paper bag with various handle options',
                'material' => 'Kraft Paper',
                'product_group' => 'paper-bag-handle',
                'image' => 'HIT_CB_010.png',
                'variants' => [
                    ['size' => '220 x 205 x 115mm (PP Cord)', 'packing_quantity' => '50 pcs/ctn', 'price' => 30.00, 'stock' => 80],
                    ['size' => '280 x 280 x 150mm (Die Cut)', 'packing_quantity' => '500 pcs/ctn', 'price' => 65.00, 'stock' => 80],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);

            // Use code as unique key
            $product = Product::updateOrCreate([
                'code' => $productData['code']
            ], $productData);

            foreach ($variants as $variant) {
                ProductVariant::updateOrCreate([
                    'product_id' => $product->id,
                    'size' => $variant['size']
                ],[
                    'packing_quantity' => $variant['packing_quantity'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                ]);
            }
        }
    }
}