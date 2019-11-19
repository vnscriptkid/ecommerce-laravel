<?php

namespace App\Http\Controllers\Cart;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\ProductVariation;
use Illuminate\Http\Request;

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

    public function update(UpdateCartItemRequest $request, ProductVariation $productVariation, Cart $cart)
    {
        $cart->updateItem($productVariation->id, $request->input('quantity'));
    }

    public function destroy(ProductVariation $productVariation, Cart $cart)
    {
        $cart->deleteItem($productVariation->id);
    }
}
