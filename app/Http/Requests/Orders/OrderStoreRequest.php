<?php

namespace App\Http\Requests\Orders;

use App\Rules\IsShippingMethodValid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => [
                'bail',
                'required',
                Rule::exists('addresses', 'id')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                })
            ],
            'shipping_method_id' => [
                'bail',
                'required',
                'exists:shipping_methods,id',
                new IsShippingMethodValid($this->address_id)
            ]
        ];
    }
}
