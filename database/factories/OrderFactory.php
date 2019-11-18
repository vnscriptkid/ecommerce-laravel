<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)->create()->id
    ];
});
