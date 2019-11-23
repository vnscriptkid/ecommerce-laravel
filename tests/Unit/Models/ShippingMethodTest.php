<?php

namespace Tests\Unit\Models;

use App\Models\ShippingMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingMethodTest extends TestCase
{
    public function test_it_has_formatted_price_from_HasPrice_trait()
    {
        $method = factory(ShippingMethod::class)->create([
            'price' => 200
        ]);
        $this->assertEquals($method->formattedPrice, '£2.00');
    }
}
