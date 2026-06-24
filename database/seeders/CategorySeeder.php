<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Corn Starch Products', 'slug' => 'corn-starch', 'icon' => 'fa-solid fa-bowl-food'],
            ['name' => 'Paper Products', 'slug' => 'paper', 'icon' => 'fa-solid fa-box'],
            ['name' => 'Utensils', 'slug' => 'utensils', 'icon' => 'fa-solid fa-utensils'],
            ['name' => 'Carry Bags', 'slug' => 'carry-bags', 'icon' => 'fa-solid fa-bag-shopping'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}