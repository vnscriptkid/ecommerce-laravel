<?php

namespace App\Cart;

use App\Models\ProductVariation;
use App\Models\User;

class Cart
{
    public function __construct(User $user)
    {
        $this->user = $user;
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
