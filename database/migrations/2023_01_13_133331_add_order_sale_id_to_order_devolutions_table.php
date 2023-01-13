<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderSaleIdToOrderDevolutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_devolutions', function (Blueprint $table) {
            $table->bigInteger('order_sale_id')->nullable()->unsigned();
            $table->index('order_sale_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_devolutions', function (Blueprint $table) {
            //
        });
    }
}
