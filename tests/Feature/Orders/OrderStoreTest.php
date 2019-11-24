<?php

namespace Tests\Feature\Orders;

use App\Models\Address;
use App\Models\Country;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_authentication()
    {
        $response = $this->json('post', '/api/orders');

        $response->assertStatus(401);
    }

    public function test_it_fails_at_validation_if_no_data_sent()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/orders'
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id', 'shipping_method_id']);
    }

    public function test_it_fails_at_validation_if_address_id_is_not_existed()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/orders',
            [
                'address_id' => 1,
                'shipping_method_id' => factory(ShippingMethod::class)->create()->id
            ]
        );

        $response->assertStatus(404);
    }

    public function test_it_fails_at_validation_if_shipping_method_id_is_not_existed()
    {
        $response = $this->jsonAs(
            $user = factory(User::class)->create(),
            'post',
            '/api/orders',
            [
                'address_id' => factory(Address::class)->create(['user_id' => $user->id])->id,
                'shipping_method_id' => 1
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['shipping_method_id']);
    }

    public function test_it_fails_at_validation_if_address_not_belongs_to_logged_user()
    {
        $response = $this->jsonAs(
            factory(User::class)->create(),
            'post',
            '/api/orders',
            [
                'address_id' => factory(Address::class)->create()->id,
                'shipping_method_id' => factory(ShippingMethod::class)->create()->id
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['address_id']);
    }

    public function test_it_fails_at_validation_if_shipping_method_not_ready_for_the_address()
    {
        $response = $this->jsonAs(
            $user = factory(User::class)->create(),
            'post',
            '/api/orders',
            [
                'address_id' => factory(Address::class)->create(['user_id' => $user->id])->id,
                'shipping_method_id' => factory(ShippingMethod::class)->create()->id
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['shipping_method_id'])
            ->assertJsonFragment([
                'shipping_method_id' => [
                    'It is not available for the address'
                ]
            ]);
    }

    public function test_it_create_an_order()
    {
        $user = factory(User::class)->create();

        $country = factory(Country::class)->create();

        $country->shippingMethods()->attach(
            $shippingMethod = factory(ShippingMethod::class)->create()
        );

        $address = factory(Address::class)->create(['user_id' => $user->id, 'country_id' => $country->id]);

        $response = $this->jsonAs(
            $user,
            'post',
            '/api/orders',
            [
                'address_id' => $address->id,
                'shipping_method_id' => $shippingMethod->id
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'address_id' => $address->id,
            'shipping_method_id' => $shippingMethod->id,
            'sub_total' => 0
        ]);
    }
}
