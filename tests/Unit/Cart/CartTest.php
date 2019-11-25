<?php

namespace Tests\Unit\Cart;

use App\Cart\Cart;
use App\Cart\Money;
use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Models\Stock;
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

    public function test_it_checks_if_cart_with_no_items_is_empty()
    {
        $user = factory(User::class)->create();

        $this->assertTrue($user->cart->isEmpty());
    }

    public function test_it_checks_if_cart_with_all_items_with_zero_quantity_is_empty()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach(
            factory(ProductVariation::class)->create(),
            ['quantity' => 0]
        );

        $this->assertTrue($cart->isEmpty());
    }

    public function test_it_checks_if_cart_is_not_empty()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach(
            factory(ProductVariation::class)->create(),
            ['quantity' => 2]
        );

        $this->assertFalse($cart->isEmpty());
    }

    public function it_calculates_subTotal_correctly()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach(
            factory(ProductVariation::class)->create(['price' => 200]),
            ['quantity' => 2]
        );

        $this->assertInstanceOf(Money::class, $cart->subTotal());
        $this->assertEquals($cart->subTotal()->amount(), 400);
    }

    public function it_calculates_subTotal_multi_items_correctly()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach([
            factory(ProductVariation::class)->create(['price' => 200])->id => ['quantity' => 2],
            factory(ProductVariation::class)->create(['price' => 150])->id => ['quantity' => 1]
        ]);

        $this->assertInstanceOf(Money::class, $cart->subTotal());
        $this->assertEquals($cart->subTotal()->amount(), 550);
    }

    public function it_should_restrict_maximum_quantity_of_item_in_cart_to_the_quantity_left_in_stock()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach([
            ($variation = factory(ProductVariation::class)->create(['price' => 200]))->id => ['quantity' => 2],
        ]);

        factory(Stock::class)->create([
            'product_variation_id' => $variation->id,
            'quantity' => 1
        ]);

        $cart->sync();
        $this->assertEquals($cart->hasChanged(), true);
        $this->assertEquals($cart->subTotal()->amount(), 100);
        $this->assertEquals($user->cart->first()->pivot->quantity, 1);
    }

    public function test_it_can_calculate_total_with_non_exist_shipping_method_id()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $variation = factory(ProductVariation::class)->create([
            'price' => 600
        ]);

        $user->cart()->attach([
            $variation->id => ['quantity' => 5]
        ]);

        $this->assertEquals($cart->total(99)->amount(), 3000);
    }

    public function test_it_can_calculate_total_with_shipping_method()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $variation = factory(ProductVariation::class)->create([
            'price' => 600
        ]);

        $user->cart()->attach([
            $variation->id => ['quantity' => 5]
        ]);

        $shippingMethod = factory(ShippingMethod::class)->create([
            'price' => 200
        ]);

        $this->assertEquals($cart->total($shippingMethod->id)->amount(), 3200);
    }

    public function test_it_has_a_list_of_items()
    {
        $cart = new Cart(
            $user = factory(User::class)->create()
        );

        $user->cart()->attach(
            $variation = factory(ProductVariation::class)->create(),
            ['quantity' => 5]
        );

        $this->assertInstanceOf(ProductVariation::class, $cart->items()->first());
        $this->assertEquals($cart->items()->first()->name, $variation->name);
        $this->assertEquals($cart->items()->first()->pivot->quantity, 5);
    }
}
