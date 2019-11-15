<?php

namespace App\Http\Resources;

class ProductResource extends ProductIndexResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'description' => $this->description,
            'variations' => []
        ]);
    }
}
