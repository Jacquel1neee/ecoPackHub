<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ===== Corn Starch Products (category_id = 1) =====
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-001',
                'name' => 'Corn Starch 2-Compartment Lunch Box (600ml)',
                'description' => 'Biodegradable corn starch material, 2 compartments for rice and side dishes',
                'packing_quantity' => '400 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '600ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-001',
                'price' => 45.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-002',
                'name' => '4oz Corn Starch Sauce Cup',
                'description' => '4oz small sauce cup, perfect for dips and condiments',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '4oz',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-002',
                'price' => 30.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-003',
                'name' => '2oz Corn Starch Sauce Cup',
                'description' => '2oz mini sauce cup for small portions of seasoning',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '2oz',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-003',
                'price' => 25.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-004',
                'name' => 'Corn Starch Bowl (800ml)',
                'description' => 'Large biodegradable bowl, suitable for noodles, rice and salads',
                'packing_quantity' => '300 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '800ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-004',
                'price' => 55.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-005',
                'name' => 'Corn Starch Bowl (400ml)',
                'description' => 'Medium biodegradable bowl for daily meals',
                'packing_quantity' => '300 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '400ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-005',
                'price' => 48.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-006',
                'name' => 'Corn Starch Bowl (450ml)',
                'description' => 'Medium biodegradable bowl for various meals',
                'packing_quantity' => '300 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '450ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-006',
                'price' => 50.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-007',
                'name' => 'Corn Starch Bowl (280ml)',
                'description' => 'Small biodegradable bowl for side dishes and soups',
                'packing_quantity' => '300 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '280ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-007',
                'price' => 40.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-008',
                'name' => 'Corn Starch 4-Compartment Lunch Box',
                'description' => '4 compartments for complete meal separation',
                'packing_quantity' => '200 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-008',
                'price' => 60.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-009',
                'name' => 'Corn Starch 3-Compartment Lunch Box',
                'description' => '3 compartments for balanced meal portions',
                'packing_quantity' => '200 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-009',
                'price' => 58.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-010',
                'name' => 'Corn Starch Sauce Container with Lid',
                'description' => 'Sauce container with tight-seal lid, leak-proof',
                'packing_quantity' => '3000 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-010',
                'price' => 65.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-011',
                'name' => 'Corn Starch 500ml Lunch Box',
                'description' => 'Single compartment lunch box, ideal for takeaways',
                'packing_quantity' => '300 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => '500ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-011',
                'price' => 42.00
            ],
            [
                'category_id' => 1,
                'code' => 'HIT-CTR-012',
                'name' => 'Oval Bio Bagasse Box with Bio Lid (1000ml)',
                'description' => 'Oval shaped bagasse box with biodegradable lid',
                'packing_quantity' => '500 pcs/ctn',
                'material' => 'Bagasse',
                'size' => '1000ml',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-CTR-012',
                'price' => 70.00
            ],

            // ===== Paper Products (category_id = 2) =====
            [
                'category_id' => 2,
                'code' => 'HIT-PPR-013',
                'name' => 'Brown Paper Bowl 8oz with Paper Lid',
                'description' => 'Brown kraft paper bowl with matching paper lid, compostable',
                'packing_quantity' => '500 pcs/ctn',
                'material' => 'Paper',
                'size' => '8oz',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-PPR-013',
                'price' => 35.00
            ],
            [
                'category_id' => 2,
                'code' => 'HIT-PPR-014',
                'name' => 'Brown Paper Bowl 26oz with Paper Lid',
                'description' => 'Large brown kraft paper bowl for big servings',
                'packing_quantity' => '500 pcs/ctn',
                'material' => 'Paper',
                'size' => '26oz',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-PPR-014',
                'price' => 50.00
            ],

            // ===== Utensils (category_id = 3) =====
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-001',
                'name' => 'Wooden Stirrer',
                'description' => 'Natural wooden stirrer, perfect for coffee and tea',
                'packing_quantity' => '10000 pcs/ctn',
                'material' => 'Wood',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/D2691E/ffffff?text=HIT-UTS-001',
                'price' => 20.00
            ],
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-002',
                'name' => 'Corn Starch Spoon',
                'description' => 'Biodegradable spoon made from corn starch',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-UTS-002',
                'price' => 28.00
            ],
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-003',
                'name' => 'Corn Starch Fork',
                'description' => 'Biodegradable fork made from corn starch',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-UTS-003',
                'price' => 28.00
            ],
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-004',
                'name' => 'Wooden Spoon',
                'description' => 'Natural wooden spoon, durable and eco-friendly',
                'packing_quantity' => '2500 pcs/ctn',
                'material' => 'Wood',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/D2691E/ffffff?text=HIT-UTS-004',
                'price' => 32.00
            ],
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-005',
                'name' => 'Wooden Fork',
                'description' => 'Natural wooden fork, sturdy and biodegradable',
                'packing_quantity' => '2500 pcs/ctn',
                'material' => 'Wood',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/D2691E/ffffff?text=HIT-UTS-005',
                'price' => 32.00
            ],
            [
                'category_id' => 3,
                'code' => 'HIT-UTS-006',
                'name' => '3-in-1 Cutlery Set',
                'description' => 'Complete cutlery set with spoon, fork and knife',
                'packing_quantity' => '500 sets/ctn',
                'material' => 'Wood/Corn Starch',
                'size' => 'Standard',
                'image' => 'https://via.placeholder.com/300x200/2e7d32/ffffff?text=HIT-UTS-006',
                'price' => 55.00
            ],

            // ===== Carry Bags (category_id = 4) =====
            [
                'category_id' => 4,
                'code' => 'HIT-CB-001',
                'name' => 'E20 Bio Singlet Bag (12 x 12")',
                'description' => 'Biodegradable singlet bag, size 12 x 12 inches',
                'packing_quantity' => '50 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '12 x 12"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-001',
                'price' => 8.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-002',
                'name' => 'E30 Bio Singlet Bag (15 x 15")',
                'description' => 'Biodegradable singlet bag, size 15 x 15 inches',
                'packing_quantity' => '50 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '15 x 15"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-002',
                'price' => 10.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-003',
                'name' => 'E40 Bio Singlet Bag (17 x 19")',
                'description' => 'Biodegradable singlet bag, size 17 x 19 inches',
                'packing_quantity' => '40 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '17 x 19"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-003',
                'price' => 12.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-004',
                'name' => 'E48 Bio Singlet Bag (18 x 22")',
                'description' => 'Biodegradable singlet bag, size 18 x 22 inches',
                'packing_quantity' => '40 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '18 x 22"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-004',
                'price' => 14.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-005',
                'name' => 'E55 Bio Singlet Bag (22 x 24")',
                'description' => 'Biodegradable singlet bag, size 22 x 24 inches',
                'packing_quantity' => '40 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '22 x 24"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-005',
                'price' => 16.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-006',
                'name' => 'E60 Bio Singlet Bag (24 x 30")',
                'description' => 'Biodegradable singlet bag, size 24 x 30 inches',
                'packing_quantity' => '40 pcs/pkt',
                'material' => 'Biodegradable Plastic',
                'size' => '24 x 30"',
                'image' => 'https://via.placeholder.com/300x200/4A7040/ffffff?text=HIT-CB-006',
                'price' => 20.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-007',
                'name' => 'Paper Bag SOS No.4 (127 x 240 x 77mm)',
                'description' => 'Brown kraft paper bag with base, self-opening style',
                'packing_quantity' => '1200 pcs/ctn',
                'material' => 'Kraft Paper',
                'size' => '127 x 240 x 77mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-007',
                'price' => 75.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-008',
                'name' => 'Paper Bag SOS No.6 (147 x 273 x 92mm)',
                'description' => 'Brown kraft paper bag with base, self-opening style',
                'packing_quantity' => '1200 pcs/ctn',
                'material' => 'Kraft Paper',
                'size' => '147 x 273 x 92mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-008',
                'price' => 85.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-009',
                'name' => 'Paper Bag SOS No.8 (154 x 314 x 100mm)',
                'description' => 'Brown kraft paper bag with base, self-opening style',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Kraft Paper',
                'size' => '154 x 314 x 100mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-009',
                'price' => 95.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-010',
                'name' => 'Paper Bag with PP Cord Handle (Medium)',
                'description' => 'Brown paper bag with PP cord handle, medium size',
                'packing_quantity' => '50 pcs/ctn',
                'material' => 'Kraft Paper + PP Cord',
                'size' => '220 x 205 x 115mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-010',
                'price' => 30.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-011',
                'name' => 'Paper Bag with Die Cut Handle (280 x 280 x 150mm)',
                'description' => 'Brown kraft paper bag with die cut handle',
                'packing_quantity' => '500 pcs/ctn',
                'material' => 'Kraft Paper',
                'size' => '280 x 280 x 150mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-011',
                'price' => 65.00
            ],
            [
                'category_id' => 4,
                'code' => 'HIT-CB-012',
                'name' => 'Paper Bag SOS No.12 (178 x 330 x 112mm)',
                'description' => 'Large brown kraft paper bag with base',
                'packing_quantity' => '1000 pcs/ctn',
                'material' => 'Kraft Paper',
                'size' => '178 x 330 x 112mm',
                'image' => 'https://via.placeholder.com/300x200/8B7355/ffffff?text=HIT-CB-012',
                'price' => 110.00
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}