<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_new_order_with_no_order_line()
    {
        $order = factory(Order::class)->create([
            'user_id' => factory(User::class)->create()->id
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id
        ]);
    }

    public function test_it_has_1_order_line()
    {
        $order = factory(Order::class)->create();

        $order->orderLines()->save(
            factory(OrderLine::class)->make()
        );

        $this->assertInstanceOf(OrderLine::class, $order->orderLines->first());
        $this->assertEquals(1, $order->orderLines->count());
    }

    public function test_it_has_5_order_lines()
    {
        $order = factory(Order::class)->create();

        $order->orderLines()->saveMany(
            factory(OrderLine::class, 5)->make()
        );

        $this->assertEquals(5, $order->orderLines->count());
    }

    public function test_it_belongs_to_an_user()
    {
        $order = factory(Order::class)->create([
            'user_id' => ($user = factory(User::class)->create())->id
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($order->user->name, $user->name);
    }

    public function test_it_has_an_address()
    {
        $address = factory(Address::class)->create();

        $order = factory(Order::class)->create([
            'address_id' => $address->id
        ]);

        $this->assertInstanceOf(Address::class, $order->address);
        $this->assertEquals($order->address->name, $address->name);
    }

    public function test_it_has_a_shipping_method()
    {
        $shippingMethod = factory(ShippingMethod::class)->create();

        $order = factory(Order::class)->create([
            'shipping_method_id' => $shippingMethod->id,
        ]);

        $this->assertInstanceOf(ShippingMethod::class, $order->shippingMethod);
        $this->assertEquals($order->shippingMethod->name, $shippingMethod->name);
    }

    public function test_it_has_pending_as_default_value_for_status()
    {
        $order = factory(Order::class)->create();

        $this->assertEquals($order->status, 'pending');
    }
}
