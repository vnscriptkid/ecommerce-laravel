<?php

namespace Tests\Unit\Models;

use App\Models\OrderLine;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationType;
use App\Models\Stock;
use App\Models\User;
use App\Order;
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

    public function test_it_should_have_0_stock_by_default()
    {
        $variation = factory(ProductVariation::class)->create([
            'product_id' => factory(Product::class)->create()->id,
            'product_variation_type_id' => factory(ProductVariationType::class)->create()->id
        ]);

        $this->assertEquals($variation->stockCount(), 0);
        $this->assertEquals($variation->inStock(), false);
    }

    public function test_it_should_get_stock_from_view_table_if_no_order()
    {
        $variation = factory(ProductVariation::class)->create([
            'product_id' => factory(Product::class)->create()->id,
            'product_variation_type_id' => factory(ProductVariationType::class)->create()->id
        ]);

        $stock = $variation->stocks()->save(
            factory(Stock::class)->make()
        );

        $variation = ProductVariation::find($variation->id);

        $this->assertEquals($variation->stockCount(), $stock->quantity);
        $this->assertEquals($variation->inStock(), true);
    }

    public function test_it_should_calculate_based_on_stocks_and_orders()
    {
        $variation = factory(ProductVariation::class)->create();

        $variation->stocks()->save(
            factory(Stock::class)->make([
                'quantity' => 200
            ])
        );

        factory(OrderLine::class)->create([
            'product_variation_id' => $variation->id,
            'quantity' => 100
        ]);

        $this->assertEquals($variation->stockCount(), 100);
        $this->assertEquals($variation->inStock(), true);
    }

    public function test_it_should_add_up_stocks_of_same_variation()
    {
        $variation = factory(ProductVariation::class)->create();

        $variation->stocks()->saveMany(
            factory(Stock::class, 3)->make([
                'quantity' => 200
            ])
        );

        $this->assertEquals($variation->stockCount(), 600);
        $this->assertEquals($variation->inStock(), true);
    }
}
