<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_formatted_price_from_HasPrice_trait()
    {
        $method = factory(ShippingMethod::class)->create([
            'price' => 200
        ]);
        $this->assertEquals($method->formattedPrice, '£2.00');
    }

    public function test_it_belongs_to_many_countries()
    {
        $shippingMethod = factory(ShippingMethod::class)->create();

        $shippingMethod->countries()->attach(
            $country = factory(Country::class)->create()
        );

        $this->assertInstanceOf(Country::class, $shippingMethod->countries->first());
        $this->assertEquals($country->name, $shippingMethod->countries->first()->name);
    }
}
