<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'sub_total' => $this->sub_total,
            'products' => ProductVariationResource::collection(
                $this->whenLoaded('productVariations')
            ),
            'address' => new AddressResource(
                $this->whenLoaded('address')
            ),
            'shipping_method' => new ShippingMethodResource(
                $this->whenLoaded('shippingMethod')
            )
        ];
    }
}
