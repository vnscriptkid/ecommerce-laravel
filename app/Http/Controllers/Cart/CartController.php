<?php

namespace App\Http\Controllers\Cart;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;

class CartController extends Controller
{
    public function __construct(Cart $cart)
    {
        $this->middleware('auth:api');
        $this->cart = $cart;
    }

    public function store(StoreCartRequest $request)
    {
        $this->cart->add($request->products);
    }
}
