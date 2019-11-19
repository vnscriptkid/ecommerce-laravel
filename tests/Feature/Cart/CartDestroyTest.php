<?php

namespace Tests\Feature\Cart;

use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CartDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('delete', '/api/cart/1');

        $response->assertStatus(401);
    }

    public function test_it_should_404_if_variation_not_found()
    {
        $user = factory(User::class)->create();

        $response = $this->jsonAs($user, 'delete', "/api/cart/1");

        $response->assertStatus(404);
    }

    public function test_it_should_delete_item_from_cart()
    {
        $user = factory(User::class)->create();

        $user->cart()->attach(
            $variation = factory(ProductVariation::class)->create(),
            ['quantity' => 4]
        );

        $this->assertEquals(DB::table('carts')->count(), 1);

        $response = $this->jsonAs($user, 'delete', "/api/cart/{$variation->id}");

        $response->assertStatus(200);

        $this->assertEquals(DB::table('carts')->count(), 0);
    }
}
