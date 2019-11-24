<?php

namespace Tests\Feature\Cart;

use App\Cart\Money;
use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_to_be_authenticated_first()
    {
        $response = $this->json('get', '/api/cart');

        $response->assertStatus(401);
    }

    public function test_it_should_be_empty_cart()
    {
        $reponse = $this->jsonAs(
            factory(User::class)->create(),
            'get',
            '/api/cart'
        );

        $reponse->assertStatus(200);
        $reponse->assertExactJson([
            'data' => [
                'products' => []
            ],
            'meta' => [
                'empty' => true,
                'subTotal' => (new Money(0))->format(),
                'changed' => false,
                'total' => (new Money(0))->format()
            ]
        ]);
    }

    public function test_it_should_have_1_item_in_cart()
    {
        $user = factory(User::class)->create();
        $user->cart()->attach([
            $user->id => factory(ProductVariation::class)->create()->id
        ]);

        $reponse = $this->jsonAs(
            $user,
            'get',
            '/api/cart'
        );

        $reponse->assertStatus(200);
        $reponse->assertJsonCount(1, 'data.products');
        $reponse->assertJsonStructure([
            'data' => [
                'products' => [
                    [
                        'id',
                        'name',
                        'price',
                        'price_varies',
                        'stock_count',
                        'in_stock',
                        'product',
                        'quantity',
                        'total',
                        'type'
                    ]
                ]
            ]
        ]);
    }

    public function test_it_should_return_correct_formatted_subTotal_in_meta_in_case_empty_cart()
    {
        $user = factory(User::class)->create();

        $reponse = $this->jsonAs(
            $user,
            'get',
            '/api/cart'
        );

        $reponse->assertStatus(200)
            ->assertJsonFragment([
                'subTotal' => '£0.00'
            ]);
    }

    public function test_it_should_return_correct_formatted_subTotal_in_meta_in_case_multi_items()
    {
        $user = factory(User::class)->create();

        $variation_1 = factory(ProductVariation::class)->create(['price' => 120]);
        $variation_2 = factory(ProductVariation::class)->create(['price' => 30]);

        $user->cart()->attach([
            $variation_1->id => ['quantity' => 2],
            $variation_2->id => ['quantity' => 2],
        ]);

        factory(Stock::class)->create(['product_variation_id' => $variation_1->id, 'quantity' => 2]);
        factory(Stock::class)->create(['product_variation_id' => $variation_2->id, 'quantity' => 2]);

        $reponse = $this->jsonAs(
            $user,
            'get',
            '/api/cart'
        );

        $reponse->assertStatus(200);

        $this->assertEquals($reponse->json('meta')['subTotal'], '£3.00');
    }

    public function test_it_should_auto_reduce_quantity_of_item_to_quantity_in_stock()
    {
        $user = factory(User::class)->create();

        $variation_1 = factory(ProductVariation::class)->create(['price' => 120]);
        $variation_2 = factory(ProductVariation::class)->create(['price' => 30]);

        $user->cart()->attach([
            $variation_1->id => ['quantity' => 2],
            $variation_2->id => ['quantity' => 2],
        ]);

        factory(Stock::class)->create(['product_variation_id' => $variation_1->id, 'quantity' => 1]);
        factory(Stock::class)->create(['product_variation_id' => $variation_2->id, 'quantity' => 1]);

        $reponse = $this->jsonAs(
            $user,
            'get',
            '/api/cart'
        );

        $reponse->assertStatus(200)
            ->assertJsonFragment([
                'changed' => true,
                'subTotal' => '£1.50'
            ]);
    }

    public function test_it_show_correct_total_with_shipping_price()
    {
        $user = factory(User::class)->create();

        $variation = factory(ProductVariation::class)->create(['price' => 200]);

        $user->cart()->attach([
            $variation->id => ['quantity' => 2],
        ]);

        factory(Stock::class)->create(['product_variation_id' => $variation->id, 'quantity' => 2]);

        $shippingMethod = factory(ShippingMethod::class)->create(['price' => 20]);

        $response = $this->jsonAs(
            $user,
            'get',
            "/api/cart?shipping_method_id={$shippingMethod->id}"
        );

        $response->assertStatus(200);
        $this->assertEquals($response->json('meta')['total'], '£4.20');
    }

    public function test_it_show_correct_total_without_shipping_price()
    {
        $user = factory(User::class)->create();

        $variation = factory(ProductVariation::class)->create(['price' => 200]);

        $user->cart()->attach([
            $variation->id => ['quantity' => 2],
        ]);

        factory(Stock::class)->create(['product_variation_id' => $variation->id, 'quantity' => 2]);

        $response = $this->jsonAs(
            $user,
            'get',
            "/api/cart"
        );

        $response->assertStatus(200);
        $this->assertEquals($response->json('meta')['total'], '£4.00');
    }
}
