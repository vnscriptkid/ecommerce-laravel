<?php

namespace App\Http\Controllers\Orders;

use App\Cart\Cart;
use App\Events\Order\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderStoreRequest;
use App\Http\Resources\OrderResource;
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
        if ($cart->isEmpty()) {
            return response(null, 400);
        }

        $order = $request->user()->orders()->create(
            array_merge(
                $request->validated(),
                ['sub_total' => $cart->subTotal()->amount()]
            )
        );

        $orderLines = $cart->items()->forSyncing();

        // [ id: { quantity } ]
        $order->productVariations()->sync($orderLines);

        // $order->load(['address', 'shippingMethod', 'productVariations']);
        event(new OrderCreated($order));

        return new OrderResource($order);
    }
}
