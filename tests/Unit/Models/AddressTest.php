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

    public function test_it_un_default_other_addresses_of_same_user_if_new_one_default_to_true()
    {
        $oldAddress = factory(Address::class)->create([
            'user_id' => ($user = factory(User::class)->create())->id,
            'default' => true
        ]);

        $newAddress = factory(Address::class)->create([
            'user_id' => $user->id,
            'default' => true
        ]);

        $this->assertEquals($user->addresses->count(), 2);
        $this->assertEquals($oldAddress->fresh()->default, 0);
        $this->assertEquals($newAddress->fresh()->default, 1);
    }
}
