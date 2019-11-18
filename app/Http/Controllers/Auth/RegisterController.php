<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function action(Request $request)
    {
        $user = User::create($request->only(['name', 'email', 'password']));
        return new PrivateUserResource($user);
    }
}
