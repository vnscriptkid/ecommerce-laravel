<?php

namespace App\Cart;

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

    protected function transformItems(array $items)
    {
        return collect($items)
            ->keyBy('id')
            ->map(function ($variation) {
                return [
                    'quantity' => $variation['quantity']
                ];
            });
    }
}
