<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductVariationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belong_to_a_product()
    {
        $product = factory(Product::class)->create();
        $variation = $product->variations()->save(
            factory(ProductVariation::class)->make()
        );

        $this->assertInstanceOf(Product::class, $variation->product);
        $this->assertEquals($variation->product->name, $product->name);
    }
}
