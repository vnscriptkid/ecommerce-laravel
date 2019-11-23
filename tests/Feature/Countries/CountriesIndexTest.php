<?php

namespace Tests\Feature\Countries;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CountriesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_list_of_countries()
    {
        factory(Country::class, 2)->create();

        $response = $this->json('GET', '/api/countries');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
