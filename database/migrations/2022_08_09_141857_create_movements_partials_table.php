<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovementsPartialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movements_partials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('plantel_id')->unsigned()->nullable();
            $table->bigInteger('reason_id')->unsigned()->nullable();
            $table->bigInteger('type_movement_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->float('costo',9,2)->nullable();
            $table->float('precio',9,2)->nullable();
            $table->integer('cantidad_entrada')->default(0);
            $table->integer('cantidad_salida')->default(0);
            $table->bigInteger('order_sales_line_id')->unsigned()->nullable();
            $table->bigInteger('usu_alta_id')->unsigned()->nullable();
            $table->bigInteger('usu_mod_id')->unsigned()->nullable();
            $table->bigInteger('usu_delete_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('usu_mod_id')->references('id')->on('users');
            $table->foreign('usu_alta_id')->references('id')->on('users');
            $table->foreign('usu_delete_id')->references('id')->on('users');
            $table->foreign('order_sales_line_id')->references('id')->on('order_sales_lines');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('type_movement_id')->references('id')->on('type_movements');
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->foreign('plantel_id')->references('id')->on('plantels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movements');
    }
}
