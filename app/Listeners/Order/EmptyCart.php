<?php

namespace App\Listeners\Order;

use App\Cart\Cart;
use App\Events\Order\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmptyCart
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function handle(OrderCreated $event)
    {
        $this->cart->empty();
    }
}
