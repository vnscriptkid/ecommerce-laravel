<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\Api\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1'
        ];
    }
}
