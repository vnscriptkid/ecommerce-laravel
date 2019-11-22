<?php

namespace App\Http\Controllers\Addresses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Addresses\StoreAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $user->load(['addresses.country']);
        return AddressResource::collection($user->addresses);
    }

    public function store(StoreAddressRequest $request)
    {
        $address = Address::make($request->validated());

        $request->user()->addresses()->save($address);

        return new AddressResource($address);
    }
}
