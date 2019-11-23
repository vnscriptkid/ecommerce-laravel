<?php

namespace Tests\Feature\ShippingMethods;

use App\Models\Address;
use App\Models\Country;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressesShippingMethodsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('GET', '/api/addresses/11/shippingMethods');

        $response->assertStatus(401);
    }

    public function test_address_not_found()
    {
        $user = factory(User::class)->create();

        $response = $this->jsonAs($user, 'GET', "/api/addresses/1/shippingMethods");

        $response->assertStatus(404);
    }

    public function test_it_requires_address_is_of_logged_user()
    {
        $randomAddress = factory(Address::class)->create();

        $response = $this->jsonAs(
            factory(User::class)->create(),
            'GET',
            "/api/addresses/{$randomAddress->id}/shippingMethods"
        );

        $response->assertStatus(403);
    }

    public function test_it_list_all_shipping_methods_of_the_address()
    {
        $address = factory(Address::class)->create([
            'user_id' => ($user = factory(User::class)->create())->id,
            'country_id' => ($country = factory(Country::class)->create())->id
        ]);

        $country->shippingMethods()->attach(
            $shippingMethod = factory(ShippingMethod::class)->create()
        );

        $response = $this->jsonAs(
            $user,
            'GET',
            "/api/addresses/{$address->id}/shippingMethods"
        );

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'name' => $shippingMethod->name,
                'price' => $shippingMethod->formattedPrice
            ]);
    }
}
