<?php

namespace App\Http\Requests\Addresses;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'address_1' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country_id' => 'required|integer|exists:countries,id',
            'default' => 'nullable|boolean'
        ];
    }
}
