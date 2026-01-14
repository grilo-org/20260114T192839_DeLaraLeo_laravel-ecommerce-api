<?php

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    private Category $category1;
    private Category $category2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category1 = Category::factory()->create(['name' => 'Electronics']);
        $this->category2 = Category::factory()->create(['name' => 'Clothing']);

        // Create products with different categories and prices
        Product::factory()->create([
            'category_id' => $this->category1->id,
            'name' => 'Laptop',
            'price' => 999.99,
            'stock' => 5,
        ]);

        Product::factory()->create([
            'category_id' => $this->category1->id,
            'name' => 'Smartphone',
            'price' => 599.99,
            'stock' => 10,
        ]);

        Product::factory()->create([
            'category_id' => $this->category2->id,
            'name' => 'T-Shirt',
            'price' => 29.99,
            'stock' => 20,
        ]);

        Product::factory()->create([
            'category_id' => $this->category2->id,
            'name' => 'Jeans',
            'price' => 79.99,
            'stock' => 15,
        ]);
    }

    public function test_can_filter_products_by_category(): void
    {
        $response = $this->getJson("/api/products?category={$this->category1->id}");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(2, $data);
        foreach ($data as $product) {
            $this->assertEquals($this->category1->id, $product['category']['id'] ?? null);
        }
    }

    public function test_can_filter_products_by_min_price(): void
    {
        $response = $this->getJson('/api/products?min_price=500');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $product) {
            $this->assertGreaterThanOrEqual(500, $product['price']);
        }
    }

    public function test_can_filter_products_by_max_price(): void
    {
        $response = $this->getJson('/api/products?max_price=100');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $product) {
            $this->assertLessThanOrEqual(100, $product['price']);
        }
    }

    public function test_can_filter_products_by_price_range(): void
    {
        $response = $this->getJson('/api/products?min_price=50&max_price=200');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $product) {
            $this->assertGreaterThanOrEqual(50, $product['price']);
            $this->assertLessThanOrEqual(200, $product['price']);
        }
    }

    public function test_can_search_products_by_name(): void
    {
        $response = $this->getJson('/api/products?search=Laptop');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(1, $data);
        $this->assertStringContainsString('Laptop', $data[0]['name']);
    }

    public function test_can_sort_products_by_price_ascending(): void
    {
        $response = $this->getJson('/api/products?sort=price');

        $response->assertStatus(200);
        $data = $response->json('data');

        $prices = array_column($data, 'price');
        $sortedPrices = $prices;
        sort($sortedPrices);

        $this->assertEquals($sortedPrices, $prices);
    }

    public function test_can_sort_products_by_price_descending(): void
    {
        $response = $this->getJson('/api/products?sort=-price');

        $response->assertStatus(200);
        $data = $response->json('data');

        $prices = array_column($data, 'price');
        $sortedPrices = $prices;
        rsort($sortedPrices);

        $this->assertEquals($sortedPrices, $prices);
    }

    public function test_can_paginate_products(): void
    {
        Product::factory()->count(20)->create(['category_id' => $this->category1->id]);

        $response = $this->getJson('/api/products?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);

        $data = $response->json('data');
        $this->assertCount(10, $data);
    }

    public function test_can_combine_multiple_filters(): void
    {
        $response = $this->getJson("/api/products?category={$this->category1->id}&min_price=500&search=phone");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertGreaterThanOrEqual(1, count($data));
        foreach ($data as $product) {
            $this->assertGreaterThanOrEqual(500, $product['price']);
            $this->assertStringContainsStringIgnoringCase('phone', $product['name']);
        }
    }
}

