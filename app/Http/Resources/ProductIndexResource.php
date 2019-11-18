<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->formattedPrice,
            'stock_count' => $this->stockCount(),
            'in_stock' => $this->inStock()
        ];
    }
}
