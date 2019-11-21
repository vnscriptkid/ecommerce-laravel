<?php

namespace Tests\Feature\Cart;

use App\Models\ProductVariation;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFunctionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_to_be_authenticated_first()
    {
        $response = $this->json('post', '/api/cart');

        $response->assertStatus(401);
    }

    public function test_it_fails_at_validation_as_body_is_empty()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/cart',
            []
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['products']);
        $response->assertSee('The products field is required.');
    }

    public function test_it_fails_at_validation_as_id_missing()
    {
        $data = [
            'products' => [
                ['quantity' => 2]
            ]
        ];

        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['products.0.id']);
    }

    public function test_it_fails_at_validation_as_quantity_missing()
    {
        $data = [
            'products' => [
                ['id' => 2]
            ]
        ];

        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['products.0.quantity']);
    }

    public function test_it_fails_at_validation_as_ids_duplicated()
    {
        $data = [
            'products' => [
                ['id' => 1, 'quantity' => 2],
                ['id' => 1, 'quantity' => 2],
            ]
        ];

        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['products.0.id', 'products.1.id']);
        $response->assertSee('has a duplicate value');
    }

    public function test_it_fails_at_validation_as_id_non_exists()
    {
        $data = [
            'products' => [
                ['id' => 1, 'quantity' => 2]
            ]
        ];

        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['products.0.id']);
        $response->assertSee('The selected products.0.id is invalid.');
    }

    public function test_it_stores_cart_to_db()
    {
        $variation = factory(ProductVariation::class)->create();

        $data = [
            'products' => [
                ['id' => $variation->id, 'quantity' => 999]
            ]
        ];

        $response = $this->jsonAs(
            $user = factory(User::class)->create(),
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'quantity' => 999,
            'user_id' => $user->id,
            'product_variation_id' => $variation->id
        ]);
    }

    public function test_it_should_add_quantity_to_current_quantity_of_variation()
    {
        $variation = factory(ProductVariation::class)->create();

        $user = factory(User::class)->create();

        $user->cart()->attach([
            $variation->id => ['quantity' => 1]
        ]);

        factory(Stock::class)->create([
            'quantity' => 1000,
            'product_variation_id' => $variation->id
        ]);

        $data = [
            'products' => [
                ['id' => $variation->id, 'quantity' => 999]
            ]
        ];

        $response = $this->jsonAs(
            $user,
            'post',
            '/api/cart',
            $data
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'quantity' => 1000,
            'user_id' => $user->id,
            'product_variation_id' => $variation->id
        ]);
    }
}
