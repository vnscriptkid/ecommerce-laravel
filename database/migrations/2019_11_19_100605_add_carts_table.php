<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // pivot table between user and product variation
        Schema::create('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedInteger('product_variation_id')->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('product_variation_id')->references('id')->on('product_variations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
}
