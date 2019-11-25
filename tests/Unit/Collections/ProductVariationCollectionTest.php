<?php

namespace Tests\Unit\Collections;

use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductVariationCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_for_syncing_method()
    {
        $user = factory(User::class)->create();

        $user->cart()->attach(
            $variation = factory(ProductVariation::class)->create(),
            ['quantity' => 2]
        );

        $this->assertEquals($user->cart->forSyncing(), [
            $variation->id => [
                'quantity' => 2
            ]
        ]);
    }
}
