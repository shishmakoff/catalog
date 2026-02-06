<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_products_list(): void
    {
        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    public function test_index_includes_category_relationship(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.category.id', $category->id);
    }

    public function test_index_filters_by_price_range(): void
    {
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 500]);
        Product::factory()->create(['price' => 1000]);

        $response = $this->getJson('/api/products?price_from=200&price_to=800');

        $response->assertStatus(200);
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(200, $product['price']);
            $this->assertLessThanOrEqual(800, $product['price']);
        }
    }

    public function test_index_filters_by_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        Product::factory()->count(3)->for($category1)->create();
        Product::factory()->count(2)->for($category2)->create();

        $response = $this->getJson("/api/products?category_id={$category1->id}");

        $response->assertStatus(200);
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertEquals($category1->id, $product['category_id']);
        }
    }

    public function test_index_filters_by_in_stock(): void
    {
        Product::factory()->inStock()->count(3)->create();
        Product::factory()->outOfStock()->count(2)->create();

        $response = $this->getJson('/api/products?in_stock=true');

        $response->assertStatus(200);
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertTrue($product['in_stock']);
        }
    }

    public function test_index_filters_by_min_rating(): void
    {
        Product::factory()->create(['rating' => 2.5]);
        Product::factory()->create(['rating' => 4.5]);
        Product::factory()->create(['rating' => 3.0]);

        $response = $this->getJson('/api/products?rating_from=3.5');

        $response->assertStatus(200);
        $products = $response->json('data');
        foreach ($products as $product) {
            $this->assertGreaterThanOrEqual(3.5, $product['rating']);
        }
    }

    public function test_index_sorts_by_price_ascending(): void
    {
        Product::factory()->create(['price' => 500]);
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 300]);

        $response = $this->getJson('/api/products?sort=price_asc');

        $response->assertStatus(200);
        $products = $response->json('data');
        $prices = array_column($products, 'price');
        $this->assertEquals([100, 300, 500], $prices);
    }

    public function test_index_sorts_by_price_descending(): void
    {
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 500]);
        Product::factory()->create(['price' => 300]);

        $response = $this->getJson('/api/products?sort=price_desc');

        $response->assertStatus(200);
        $products = $response->json('data');
        $prices = array_column($products, 'price');
        $this->assertEquals([500, 300, 100], $prices);
    }

    public function test_index_sorts_by_rating_descending(): void
    {
        Product::factory()->create(['rating' => 3.0]);
        Product::factory()->create(['rating' => 5.0]);
        Product::factory()->create(['rating' => 4.0]);

        $response = $this->getJson('/api/products?sort=rating_desc');

        $response->assertStatus(200);
        $products = $response->json('data');
        $ratings = array_column($products, 'rating');
        $this->assertEquals([5.0, 4.0, 3.0], $ratings);
    }

    public function test_index_sorts_by_newest(): void
    {
        $oldProduct = Product::factory()->create();
        $oldProduct->created_at = now()->subDays(2);
        $oldProduct->save();

        $newProduct = Product::factory()->create();
        $newProduct->created_at = now();
        $newProduct->save();

        $response = $this->getJson('/api/products?sort=newest');

        $response->assertStatus(200);
        $products = $response->json('data');
        $this->assertEquals($newProduct->id, $products[0]['id']);
    }

    public function test_index_validates_price_from_is_numeric(): void
    {
        $response = $this->getJson('/api/products?price_from=invalid');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['price_from']]);
    }

    public function test_index_validates_price_to_is_numeric(): void
    {
        $response = $this->getJson('/api/products?price_to=invalid');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['price_to']]);
    }

    public function test_index_validates_category_id_exists(): void
    {
        $response = $this->getJson('/api/products?category_id=99999');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['category_id']]);
    }

    public function test_index_validates_rating_from_range(): void
    {
        $response = $this->getJson('/api/products?rating_from=10');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['rating_from']]);
    }

    public function test_index_validates_sort_option(): void
    {
        $response = $this->getJson('/api/products?sort=invalid_sort');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['sort']]);
    }

    public function test_index_validates_per_page_maximum(): void
    {
        $response = $this->getJson('/api/products?per_page=200');

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['per_page']]);
    }

    public function test_index_respects_per_page_parameter(): void
    {
        Product::factory()->count(25)->create();

        $response = $this->getJson('/api/products?per_page=5');

        $response->assertStatus(200)
            ->assertJsonPath('per_page', 5)
            ->assertJsonCount(5, 'data');
    }

    public function test_show_returns_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'price',
                    'category_id',
                    'in_stock',
                    'rating',
                    'category',
                ],
            ])
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_show_includes_category_relationship(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.category.id', $category->id)
            ->assertJsonPath('data.category.name', $category->name);
    }

    public function test_show_returns_404_for_nonexistent_product(): void
    {
        $response = $this->getJson('/api/products/99999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors' => ['id'],
            ])
            ->assertJsonPath('message', 'Товар не найден');
    }

    public function test_show_validates_id_is_numeric(): void
    {
        $response = $this->getJson('/api/products/invalid');

        $response->assertStatus(404);
    }
}
