<?php

namespace App\Http\Resources;

use App\Cart\Money;

class CartProductVariationResource extends ProductVariationResource
{
    public function toArray($request)
    {
        $total = (new Money($this->price * $this->pivot->quantity))->format();

        return array_merge(parent::toArray($request), [
            'product' => new ProductIndexResource($this->product),
            'quantity' => $this->pivot->quantity,
            'total' => $total
        ]);
    }
}
