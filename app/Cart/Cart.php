<?php

namespace App\Cart;

use App\Models\ProductVariation;
use App\Models\ShippingMethod;
use App\Models\User;

class Cart
{
    protected $user;
    protected $changed;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->changed = false;
    }

    public function items()
    {
        return $this->user->cart;
    }

    public function add(array $items = [])
    {
        // items : [ { id, quantity } ]
        $cartItems = $this->transformItems($items);

        $this->user->cart()->syncWithoutDetaching($cartItems);
    }

    public function updateItem($variationId, $quantity)
    {
        // in case $variationId does not exist, do not throw error here
        $this->user->cart()->updateExistingPivot($variationId, [
            'quantity' => $quantity
        ]);
    }

    public function isEmpty()
    {
        return $this->user->cart->sum('pivot.quantity') === 0;
    }

    public function deleteItem($variationId)
    {
        $this->user->cart()->detach($variationId);
    }

    public function empty()
    {
        $this->user->cart()->detach();
    }

    // the total without taxes and shipping fees
    public function subTotal()
    {
        $subTotal = $this->user->cart->sum(function ($item) {
            return $item->price * $item->pivot->quantity;
        });
        return new Money($subTotal);
    }

    public function total($shippingMethodId)
    {

        if ($shippingMethodId && $shippingMethod = ShippingMethod::find($shippingMethodId)) {
            return $this->subTotal()->add(
                $shippingMethod->getMoneyObj()
            );
        }
        return $this->subTotal();
    }

    // sync cart with stocks
    public function sync()
    {
        $dataToSync = [];

        $this->user->cart->each(function ($variation) use (&$dataToSync) {
            $leftInStock = $variation->stockCount();
            $orderedQuantity = $variation->pivot->quantity;

            if ($orderedQuantity > $leftInStock) {
                $dataToSync[$variation->id] = ['quantity' => $leftInStock];
                //another way
                // $variation->pivot->update([ 'quantity' => $leftInStock]);
            }
        });

        if (count($dataToSync) > 0) {
            // batch sync to reduce # of queries
            $this->changed = true;
            return $this->user->cart()->syncWithoutDetaching($dataToSync);
        }
        return false;
    }

    public function hasChanged()
    {
        return $this->changed;
    }

    protected function transformItems(array $items)
    {
        return collect($items)
            ->keyBy('id')
            ->map(function ($variation) {
                return [
                    'quantity' => $variation['quantity'] + $this->getCurrentQuantityOfVariation($variation['id'])
                ];
            });
    }

    protected function getCurrentQuantityOfVariation($id)
    {
        $foundVariation = $this->user->cart->find($id);

        if ($foundVariation) {
            return $foundVariation->pivot->quantity;
        }

        return 0;
    }
}
