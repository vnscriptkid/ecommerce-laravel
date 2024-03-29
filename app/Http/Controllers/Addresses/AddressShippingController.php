<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingMethodResource;
use App\Models\Address;

class AddressShippingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Address $address)
    {
        $this->authorize('view', $address);

        return ShippingMethodResource::collection($address->country->shippingMethods);
    }
}
