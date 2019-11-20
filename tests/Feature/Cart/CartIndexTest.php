<?php

namespace Tests\Feature\Cart;

use App\Models\ProductVariation;
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
                        'subTotal'
                    ]
                ]
            ]
        ]);
    }
}
