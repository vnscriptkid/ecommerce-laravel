<?php

namespace Tests\Unit\Cart;

use App\Cart\Cart;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_store_items_to_carts()
    {
        $variation = factory(ProductVariation::class)->create();

        $user = factory(User::class)->create();
        $cart = new Cart($user);

        $items = [
            [
                'id' => $variation->id,
                'quantity' => 20
            ]
        ];

        $cart->add($items);

        $this->assertEquals(DB::table('carts')->count(), 1);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_variation_id' => $variation->id,
            'quantity' => 20
        ]);
    }

    public function test_it_should_update_quantity_of_cart_item()
    {
        $variation = factory(ProductVariation::class)->create();

        $user = factory(User::class)->create();
        $cart = new Cart($user);

        $items = [
            [
                'id' => $variation->id,
                'quantity' => 20
            ]
        ];

        $cart->add($items);
        $cart->updateItem($variation->id, 40);

        $this->assertEquals(DB::table('carts')->count(), 1);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_variation_id' => $variation->id,
            'quantity' => 40
        ]);
    }

    public function test_it_should_delete_item_from_cart()
    {
        $variation = factory(ProductVariation::class)->create();

        $user = factory(User::class)->create();
        $cart = new Cart($user);

        $items = [
            [
                'id' => $variation->id,
                'quantity' => 20
            ]
        ];

        $cart->add($items);
        $cart->deleteItem($variation->id);

        $this->assertEquals(DB::table('carts')->count(), 0);
    }

    public function test_it_can_empty_the_whole_cart()
    {
        $user = factory(User::class)->create();
        $user->cart()->attach([
            factory(ProductVariation::class)->create()->id => ['quantity' => 2],
            factory(ProductVariation::class)->create()->id => ['quantity' => 3],
        ]);

        $this->assertEquals($user->cart->count(), 2);
        $cart = new Cart($user);

        $cart->empty();
        $this->assertEquals($user->fresh()->cart->count(), 0);
    }
}
