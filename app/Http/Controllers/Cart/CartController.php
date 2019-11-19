<?php

namespace App\Http\Controllers\Cart;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(StoreCartRequest $request, Cart $cart)
    {
        $cart->add($request->products);
    }
}
