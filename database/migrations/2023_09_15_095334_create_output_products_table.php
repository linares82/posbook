<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutputProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('output_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movement_id')->nullable();
            $table->unsignedBigInteger('ln_cash_box_id')->nullable();
            $table->integer('cantidad_antes_descuento')->nullable();
            $table->integer('cantidad_descontada')->nullable();
            $table->integer('cantidad_restante')->nullable();
            $table->unsignedBigInteger('usu_alta_id')->nullable();
            $table->unsignedBigInteger('usu_mod_id')->nullable();
            $table->unsignedBigInteger('usu_delete_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('movement_id')->references('id')->on('movements');
            $table->foreign('ln_cash_box_id')->references('id')->on('ln_cash_boxes');
            $table->foreign('usu_mod_id')->references('id')->on('users');
            $table->foreign('usu_alta_id')->references('id')->on('users');
            $table->foreign('usu_delete_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('output_products');
    }
}
