<?php

namespace Tests\Feature\Addresses;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressesStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('post', '/api/addresses');

        $response->assertStatus(401);
    }

    public function test_it_fails_at_validation_if_no_body()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/addresses',
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'address_1', 'city', 'postal_code', 'country_id'
            ]);
    }

    public function test_it_fails_at_validation_if_country_id_does_not_exist_in_db()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/addresses',
            [
                'name' => 'Ba Dinh',
                'address_1' => 'Metropolis',
                'city' => 'Ha Noi',
                'postal_code' => '100000',
                'country_id' => 500
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'country_id'
            ]);
    }

    public function test_it_stores_address_and_returns_serialized_data()
    {
        $country = factory(Country::class)->create();

        $response = $this->jsonAs(
            $user = factory(User::class)->create(),
            'post',
            '/api/addresses',
            $data = [
                'name' => 'Ba Dinh',
                'address_1' => 'Metropolis',
                'city' => 'Ha Noi',
                'postal_code' => '100000',
                'country_id' => $country->id
            ]
        );

        $this->assertDatabaseHas('addresses', array_merge($data, [
            'user_id' => $user->id
        ]));

        $response->assertStatus(201)
            ->assertJsonFragment([
                'country' => [
                    'name' => $country->name,
                    'code' => $country->code
                ]
            ]);
    }
}
