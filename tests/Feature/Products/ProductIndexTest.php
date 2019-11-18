<?php

namespace Tests\Feature\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_no_products()
    {
        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonFragment(['data' => []]);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_it_returns_1_product_serialized()
    {
        $product = factory(Product::class)->create();

        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'data' => [
                [
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->formattedPrice,
                    'stock_count' => $product->stockCount(),
                    'in_stock' => $product->inStock()
                ]
            ]
        ]);
    }

    public function test_it_returns_5_products_serialized_in_correct_order()
    {
        $products = factory(Product::class, 5)->create();

        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
        $response->assertSeeInOrder(
            $products->map(function ($product) {
                return $product->name;
            })->toArray()
        );
    }

    public function test_it_paginates_by_10()
    {
        $products = factory(Product::class, 15)->create();

        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertSeeInOrder(
            $products->splice(0, 10)->map(function ($product) {
                return $product->name;
            })->toArray()
        );
    }

    public function test_it_should_filtered_by_category_name_passed_as_query_param()
    {
        $category = factory(Category::class)->create();
        $category->products()->save(
            $product = factory(Product::class)->create()
        );
        factory(Product::class)->create();

        // Act 1
        $response = $this->json('GET', '/api/products?category=nonexist');

        // Assert 1
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        // Act 2
        $response = $this->json('GET', "/api/products?category={$category->name}");

        // Assert 2
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertSee($product->name);
    }
}
