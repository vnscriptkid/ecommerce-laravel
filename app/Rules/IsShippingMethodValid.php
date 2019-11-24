<?php

namespace App\Rules;

use App\Models\Address;
use Illuminate\Contracts\Validation\Rule;

class IsShippingMethodValid implements Rule
{
    protected $address_id;

    public function __construct($address_id)
    {
        $this->address_id = $address_id;
    }

    public function passes($attribute, $shipping_method_id)
    {
        if (!$address = Address::findOrFail($this->address_id)) {
            return false;
        }

        return $address->country->shippingMethods->contains('id', $shipping_method_id);
    }

    public function message()
    {
        return 'It is not available for the address';
    }
}
