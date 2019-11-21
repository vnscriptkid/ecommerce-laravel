<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_belong_to_one_user()
    {
        $address = factory(Address::class)->create([
            'user_id' => ($user = factory(User::class)->create())->id
        ]);

        $this->assertInstanceOf(User::class, $address->user);
        $this->assertEquals($user->name, $address->user->name);
    }

    public function test_address_has_a_country()
    {
        $country = factory(Country::class)->create();

        $address = factory(Address::class)->create([
            'country_id' => $country->id
        ]);

        $this->assertInstanceOf(Country::class, $address->country);
        $this->assertEquals($address->country->code, $country->code);
    }
}
