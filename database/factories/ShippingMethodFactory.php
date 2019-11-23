<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ShippingMethod;
use Faker\Generator as Faker;

$factory->define(ShippingMethod::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name(),
        'price' => $faker->randomNumber()
    ];
});
