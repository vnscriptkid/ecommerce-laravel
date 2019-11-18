<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderLine;
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
}
