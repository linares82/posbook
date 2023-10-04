<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPlantelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_plantel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('plantel_id')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->double('saldo_ingresos',12,2)->nullable();
            $table->double('saldo_egresos',12,2)->nullable();
            $table->double('diferencia',12,2)->nullable();
            $table->unsignedBigInteger('usu_alta_id')->nullable();
            $table->unsignedBigInteger('usu_mod_id')->nullable();
            $table->unsignedBigInteger('usu_delete_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('users');
            $table->foreign('plantel_id')->references('id')->on('users');
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
        Schema::dropIfExists('account_plantel');
    }
}
