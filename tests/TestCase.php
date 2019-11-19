<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Contracts\JWTSubject;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function jsonAs(JWTSubject $user, $method, $uri, array $data = [], array $headers = [])
    {
        $token = auth()->tokenById($user->id);

        return $this->json($method, $uri, $data, array_merge($headers, [
            'Authorization' => 'Bearer' . $token
        ]));
    }
}
