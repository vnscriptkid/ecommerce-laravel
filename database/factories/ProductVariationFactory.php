<?php

namespace Database\Factories;

use App\Models\ProductVariation;
use Faker\Generator as Faker;

$factory->define(ProductVariation::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name()
    ];
});
