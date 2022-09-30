<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDevolutionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_devolution_lines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_devolution_id')->unsigned()->nullable();
            $table->bigInteger('plantel_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->integer('cantidad')->nullable();
            $table->string('contacto')->nullable();
            $table->integer('bnd_salida_registrada')->nullable();
            $table->bigInteger('usu_alta_id')->unsigned()->nullable();
            $table->bigInteger('usu_mod_id')->unsigned()->nullable();
            $table->bigInteger('usu_delete_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('usu_mod_id')->references('id')->on('users');
            $table->foreign('usu_alta_id')->references('id')->on('users');
            $table->foreign('usu_delete_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('plantel_id')->references('id')->on('plantels');
            $table->foreign('order_devolution_id')->references('id')->on('order_devolutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_devolution_lines');
    }
}
