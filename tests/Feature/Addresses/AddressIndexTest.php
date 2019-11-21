<?php

namespace Tests\Feature\Addresses;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('get', '/api/addresses');

        $response->assertStatus(401);
    }

    public function test_it_should_return_list_of_serialized_addresses()
    {

        $user = factory(User::class)->create();

        $address = factory(Address::class)->create([
            'user_id' => $user->id
        ]);

        $response = $this->jsonAs(
            $user,
            'get',
            '/api/addresses'
        );

        $response->assertStatus(200)
            ->assertJsonFragment([
                "id" => $address->id,
                "name" => $address->name,
                "city" => $address->city,
                "postal_code" => $address->postal_code,
                "country" => [
                    "name" => $address->country->name,
                    "code" => $address->country->code
                ]
            ]);
    }
}
