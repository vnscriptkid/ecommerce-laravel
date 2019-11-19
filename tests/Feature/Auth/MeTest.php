<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_fail_if_no_token_included()
    {
        $response = $this->json('GET', '/api/auth/me');

        $response->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_it_should_return_credentials_token_valid()
    {
        // Arrange
        $user = factory(User::class)->create();

        // Act
        // $response = $this->actingAs($user)->json('GET', '/api/auth/me');
        $response = $this->jsonAs($user, 'GET', '/api/auth/me');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email']
            ]);
    }
}
