<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class RegistrationTest extends TestCase
{

    use RefreshDatabase;

    public function test_it_fails_at_validation()
    {
        $response = $this->post('/api/auth/register', []);

        $response->assertStatus(422);

        $response->assertExactJson([
            "errors" => [
                "email" => [
                    "The email field is required."
                ],
                "name" => [
                    "The name field is required."
                ],
                "password" => [
                    "The password field is required."
                ]
            ]
        ]);
        $response->assertJsonValidationErrors(['email', 'password', 'name']);
    }

    public function test_it_fails_as_email_invalid()
    {
        $response = $this->post('/api/auth/register', ['email' => 'bad email']);

        $response->assertStatus(422);

        $response->assertSee('The email must be a valid email address.');
    }

    public function test_it_fails_as_email_is_in_use()
    {

        $user = factory(User::class)->create();

        $response = $this->post('/api/auth/register', ['email' => $user->email]);

        $response->assertStatus(422);

        $response->assertSee('The email has already been taken.');
    }

    public function test_it_fails_as_password_too_short()
    {
        $response = $this->post('/api/auth/register', ['email' => 'thanh@gmail.com', 'password' => '123']);

        $response->assertStatus(422);

        $response->assertSee('The password must be at least 6 characters.');
    }

    public function test_it_responds_new_user_in_case_of_success()
    {
        $response = $this->post('/api/auth/register', ['email' => 'thanh@gmail.com', 'password' => '123456', 'name' => 'thanh']);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'email'
            ]
        ]);
    }

    public function test_it_registers_new_user()
    {
        $response = $this->post('/api/auth/register', $user = ['email' => 'thanh@gmail.com', 'password' => '123456', 'name' => 'thanh']);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', Arr::only($user, ['email', 'name']));
    }
}
