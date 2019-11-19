<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\Api\FormRequest;

class StoreCartRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.id' => 'required|distinct|exists:product_variations,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
}
