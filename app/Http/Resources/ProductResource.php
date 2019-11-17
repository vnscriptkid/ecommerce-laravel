<?php

namespace App\Http\Resources;

class ProductResource extends ProductIndexResource
{
    public function toArray($request)
    {
        // dd($this->variations->groupBy('type.name'));
        return array_merge(parent::toArray($request), [
            'description' => $this->description,
            'variations' => ProductVariationResource::collection(
                $this->variations->groupBy('type.name')
            )
        ]);
    }
}
