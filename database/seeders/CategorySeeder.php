<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Apparel and fashion items'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and reading materials'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and accessories'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['slug' => $categoryData['slug']], $categoryData);
        }

        $electronics = Category::where('slug', 'electronics')->first();
        if ($electronics) {
            Category::firstOrCreate(
                ['slug' => 'smartphones'],
                [
                    'name' => 'Smartphones',
                    'slug' => 'smartphones',
                    'description' => 'Mobile phones and smartphones',
                    'parent_id' => $electronics->id,
                ]
            );

            Category::firstOrCreate(
                ['slug' => 'laptops'],
                [
                    'name' => 'Laptops',
                    'slug' => 'laptops',
                    'description' => 'Laptop computers',
                    'parent_id' => $electronics->id,
                ]
            );
        }
    }
}

