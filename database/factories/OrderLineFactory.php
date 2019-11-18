<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\ProductVariation;
use Faker\Generator as Faker;

$factory->define(OrderLine::class, function (Faker $faker) {
    return [
        'quantity' => $faker->randomNumber(),
        'product_variation_id' => factory(ProductVariation::class)->create()->id,
        'order_id' => factory(Order::class)->create()->id
    ];
});
