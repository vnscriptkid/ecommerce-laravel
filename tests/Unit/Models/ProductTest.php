<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_route_key_name()
    {
        $product = new Product();
        $this->assertTrue($product->getRouteKeyName() === 'slug');
    }

    public function test_product_belong_to_category()
    {
        $product = factory(Product::class)->create();
        $product->categories()->save(
            factory(Category::class)->create()
        );

        $this->assertEquals($product->categories->count(), 1);
        $this->assertInstanceOf(Category::class, $product->categories->first());
    }
}
