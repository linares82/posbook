<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('costo',9,2)->nullable();
            $table->double('precio',9,2)->nullable();
            $table->integer('bnd_activo')->default(1)->nullable();
            $table->integer('bnd_ofertable')->default(1)->nullable();
            $table->bigInteger('period_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->bigInteger('usu_alta_id')->unsigned()->nullable();
            $table->bigInteger('usu_mod_id')->unsigned()->nullable();
            $table->bigInteger('usu_delete_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('usu_mod_id')->references('id')->on('users');
            $table->foreign('usu_alta_id')->references('id')->on('users');
            $table->foreign('usu_delete_id')->references('id')->on('users');
            $table->foreign('period_id')->references('id')->on('periods');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
