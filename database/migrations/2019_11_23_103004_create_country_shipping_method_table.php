<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryShippingMethodTable extends Migration
{
    public function up()
    {
        Schema::create('country_shipping_method', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->index();
            $table->unsignedInteger('shipping_method_id')->index();

            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
        });
    }

    public function down()
    {
        Schema::dropIfExists('country_shipping_method');
    }
}
