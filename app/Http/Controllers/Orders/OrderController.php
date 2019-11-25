<?php

namespace App\Http\Controllers\Orders;

use App\Cart\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function store(OrderStoreRequest $request, Cart $cart)
    {
        $order = $request->user()->orders()->create(
            array_merge(
                $request->validated(),
                ['sub_total' => $cart->subTotal()->amount()]
            )
        );

        $orderLines = $cart->items()->keyBy('id')->map(function ($item) {
            return [
                'quantity' => $item->pivot->quantity
            ];
        });

        // [ id: { quantity } ]
        $order->productVariations()->sync($orderLines);
    }
}
