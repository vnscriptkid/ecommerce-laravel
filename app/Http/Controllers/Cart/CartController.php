<?php

namespace App\Http\Controllers\Cart;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\StoreCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
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

    // GET /api/cart
    public function index(Request $request, Cart $cart)
    {

        $cart->sync();

        $user = $request->user()->load([
            'cart.product',
            'cart.stock',
            'cart.product.variations.stock',
            'cart.type'
        ]);

        return (new CartResource($user->cart))->additional([
            'meta' => $this->meta($cart)
        ]);
    }

    protected function meta($cart)
    {
        return [
            'empty' => $cart->isEmpty(),
            'subTotal' => $cart->subTotal()->format(),
            'changed' => $cart->hasChanged()
        ];
    }
}
