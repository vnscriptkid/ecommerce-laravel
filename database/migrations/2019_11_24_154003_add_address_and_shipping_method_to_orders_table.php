<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndShippingMethodToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('address_id')->index();
            $table->unsignedInteger('shipping_method_id')->index();

            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['address_id']);
            $table->dropIndex(['shipping_method_id']);

            $table->dropColumn('address_id');
            $table->dropColumn('shipping_method_id');
        });
    }
}
