<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function action(Request $request)
    {
        User::create($request->only(['name', 'email', 'password']));
    }
}
