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
}
