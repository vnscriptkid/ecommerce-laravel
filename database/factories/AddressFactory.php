<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use App\Models\Country;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'address_1' => $faker->name(),
        'city' => $faker->name(),
        'postal_code' => $faker->name(),
        'user_id' => factory(User::class)->create()->id,
        'country_id' => factory(Country::class)->create()
    ];
});
