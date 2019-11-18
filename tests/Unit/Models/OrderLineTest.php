<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLineTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_new_order_line()
    {
        $orderLine = factory(OrderLine::class)->create();
        $this->assertDatabaseHas('order_lines', [
            'quantity' => $orderLine->quantity
        ]);
    }

    public function test_it_belongs_to_an_order()
    {
        $orderLine = factory(OrderLine::class)->create();
        $this->assertInstanceOf(Order::class, $orderLine->order);
    }
}
