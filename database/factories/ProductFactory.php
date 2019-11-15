<?php

namespace Database\Factories;

use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->unique()->name(),
        'slug' => Str::slug($name),
        'description' => $faker->sentence(6),
        'price' => $faker->randomNumber()
    ];
});
