<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationType;
use App\Models\Stock;
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

    public function test_it_has_a_variation_type()
    {
        $variation = factory(ProductVariation::class)->create([
            'product_id' => factory(Product::class)->create()->id,
            'product_variation_type_id' => factory(ProductVariationType::class)->create()->id
        ]);

        $this->assertInstanceOf(ProductVariationType::class, $variation->type);
    }

    public function test_it_should_inherit_product_price_in_case_null()
    {
        $variation = factory(ProductVariation::class)->create([
            'product_id' => factory(Product::class)->create([
                'price' => 123
            ])->id,
            'product_variation_type_id' => factory(ProductVariationType::class)->create()->id
        ]);

        $this->assertEquals($variation->formattedPrice, '£1.23');
    }

    public function test_it_has_many_stocks()
    {
        $variation = factory(ProductVariation::class)->create([
            'product_id' => factory(Product::class)->create()->id,
            'product_variation_type_id' => factory(ProductVariationType::class)->create()->id
        ]);

        $stock = $variation->stocks()->save(
            factory(Stock::class)->make()
        );

        $this->isInstanceOf(Stock::class, $variation->stocks->first());
        $this->assertEquals($variation->stocks->count(), 1);
        $this->assertEquals($variation->stocks->first()->quantity, $stock->quantity);
    }
}
