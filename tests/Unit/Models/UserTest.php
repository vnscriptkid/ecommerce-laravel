<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function test_it_should_create_hash_password()
    {
        $user = User::create([
            'name' => 'thanh',
            'email' => 'thanh@gmail.com',
            'password' => '123456'
        ]);
        $this->assertNotEquals($user->password, '123456');
    }
}
