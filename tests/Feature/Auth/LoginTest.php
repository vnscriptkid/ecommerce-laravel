<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_at_validation()
    {
        $response = $this->json('POST', '/api/auth/login', []);

        $response->assertJsonValidationErrors(['email', 'password']);

        $response->assertSeeInOrder(
            [
                'The email field is required',
                'The password field is required.'
            ]
        );
    }

    public function test_it_fails_at_validation_as_email_invalid()
    {
        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'fake',
            'password' => '123456'
        ]);

        $response->assertJsonValidationErrors(['email']);

        $response->assertSee('The email must be a valid email address.');
    }

    public function test_it_fails_at_validation_as_password_too_short()
    {
        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'thanh@gmail.com',
            'password' => '12345'
        ]);

        $response->assertJsonValidationErrors(['password']);

        $response->assertSee('The password must be at least 6 characters.');
    }

    public function test_it_fails_as_credentials_is_invalid()
    {
        User::create([
            'email' => 'thanh@gmail.com',
            'password' => '123456',
            'name' => 'Thanh'
        ]);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'thanh@gmail.com',
            'password' => '654321'
        ]);

        $response->assertStatus(422)->assertSee('Invalid credentials');
    }

    public function test_it_logins_and_gets_back_token()
    {
        User::create([
            'email' => 'thanh@gmail.com',
            'password' => '123456',
            'name' => 'Thanh'
        ]);

        $response = $this->json('POST', '/api/auth/login', [
            'email' => 'thanh@gmail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email'],
                'meta' => ['token']
            ]);
    }
}
