<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_get_route_key_name()
    {
        $product = new Product();
        $this->assertTrue($product->getRouteKeyName() === 'slug');
    }
}
