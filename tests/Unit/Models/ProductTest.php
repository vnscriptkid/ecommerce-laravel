<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_it_has_many_variations()
    {
        $product = factory(Product::class)->create();
        $product->variations()->save(
            factory(ProductVariation::class)->make()
        );

        $this->assertInstanceOf(ProductVariation::class, $product->variations->first());
        $this->assertEquals($product->variations->count(), 1);
    }

    public function test_it_should_format_price()
    {
        // £1.00
        $product = factory(Product::class)->create([
            'price' => 100
        ]);
        $this->assertEquals($product->formattedPrice, '£1.00');
    }

    public function test_it_should_count_stocks_of_all_its_variations()
    {
        $product = factory(Product::class)->create();

        $product->variations()->save(
            $variation = factory(ProductVariation::class)->make()
        );

        factory(Stock::class)->create([
            'quantity' => 600,
            'product_variation_id' => $variation->id
        ]);

        $this->assertEquals($product->stockCount(), 600);
        $this->assertEquals($product->inStock(), true);
    }

    public function test_case_of_nothing_in_stock()
    {
        $product = factory(Product::class)->create();

        $this->assertEquals($product->stockCount(), 0);
        $this->assertEquals($product->inStock(), false);
    }
}
