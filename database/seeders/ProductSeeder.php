<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $existingProductsCount = Product::count();
        
        if ($existingProductsCount >= 100) {
            $this->command->info("Products already seeded ({$existingProductsCount} products found). Skipping...");
            return;
        }

        $productsToCreate = 100 - $existingProductsCount;

        for ($i = 0; $i < $productsToCreate; $i++) {
            $category = $categories->random();
            Product::factory()->forCategory($category)->create();
        }

        $this->command->info("Created {$productsToCreate} products. Total: " . Product::count());
    }
}

