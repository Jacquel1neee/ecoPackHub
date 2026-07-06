<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Cups', 'slug' => 'cups', 'icon' => 'fa-solid fa-mug-saucer'],
            ['name' => 'Container', 'slug' => 'container', 'icon' => 'fa-solid fa-box'],
            ['name' => 'Utensil', 'slug' => 'utensil', 'icon' => 'fa-solid fa-utensils'],
            ['name' => 'Carry Bag', 'slug' => 'carry-bag', 'icon' => 'fa-solid fa-bag-shopping'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}