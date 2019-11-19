<?php

namespace Tests\Feature\Cart;

use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('patch', '/api/cart/1');

        $response->assertStatus(401);
    }

    public function test_it_should_404_if_variation_not_found()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'patch',
            '/api/cart/1'
        );

        $response->assertStatus(404);
    }

    public function test_it_should_fail_at_validation_if_quantity_is_non_numeric()
    {
        $user = factory(User::class)->create();

        $user->cart()->attach(
            ($variation = factory(ProductVariation::class)->create())->id
        );

        $response = $this->jsonAs(
            $user,
            'patch',
            "/api/cart/{$variation->id}"
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    }

    public function test_it_should_not_update_item_in_other_cart()
    {
        // Arrange 1
        $user_1 = factory(User::class)->create();

        $user_1->cart()->attach(
            ($variation_1 = factory(ProductVariation::class)->create())->id,
            ['quantity' => 1]

        );
        // Arrange 2
        $user_2 = factory(User::class)->create();

        $user_2->cart()->attach(
            ($variation_2 = factory(ProductVariation::class)->create())->id,
            ['quantity' => 2]
        );

        // Act
        $response = $this->jsonAs(
            $user_1,
            'patch',
            "/api/cart/{$variation_2->id}",
            ['quantity' => 5]
        );

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'product_variation_id' => $variation_1->id,
            'user_id' => $user_1->id,
            'quantity' => 1
        ]);
        $this->assertDatabaseHas('carts', [
            'product_variation_id' => $variation_2->id,
            'user_id' => $user_2->id,
            'quantity' => 2
        ]);
    }

    public function test_it_should_update_item_quantity_in_his_own_cart()
    {
        $user = factory(User::class)->create();

        $user->cart()->attach(
            ($variation = factory(ProductVariation::class)->create())->id,
            ['quantity' => 5]
        );

        $response = $this->jsonAs(
            $user,
            'patch',
            "/api/cart/{$variation->id}",
            ['quantity' => 6]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'product_variation_id' => $variation->id,
            'user_id' => $user->id,
            'quantity' => 6
        ]);
    }
}
