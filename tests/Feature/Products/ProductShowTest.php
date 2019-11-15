<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_return_404_if_not_found()
    {
        $response = $this->get('/api/products/1');
        $response->assertStatus(404);
    }

    public function test_it_should_return_correct_product_serialized()
    {
        $product = factory(Product::class)->create();

        $response = $this->get("/api/products/{$product->slug}");
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'description' => $product->description,
                'variations' => []
            ]
        ]);
    }
}
