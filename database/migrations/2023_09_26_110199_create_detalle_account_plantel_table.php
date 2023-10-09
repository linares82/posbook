<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleAccountPlantelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_account_plantel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_plantel_id')->nullable();
            $table->unsignedBigInteger('ln_cash_box_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->double('monto_ingreso',12,2)->nullable();
            $table->double('monto_egreso',12,2)->nullable();
            $table->double('saldo_ingresos',12,2)->nullable();
            $table->double('saldo_egresos',12,2)->nullable();
            $table->unsignedBigInteger('usu_alta_id')->nullable();
            $table->unsignedBigInteger('usu_mod_id')->nullable();
            $table->unsignedBigInteger('usu_delete_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_plantel_id')->references('id')->on('account_plantel');
            $table->foreign('ln_cash_box_id')->references('id')->on('ln_cash_boxes');
            $table->foreign('expense_id')->references('id')->on('expenses');
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
        Schema::dropIfExists('detalle_account_plantel');
    }
}
