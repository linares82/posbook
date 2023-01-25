<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CashBoxToAssign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_box_to_assigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('usu_alta_id')->unsigned()->nullable();
            $table->bigInteger('usu_mod_id')->unsigned()->nullable();
            $table->bigInteger('usu_delete_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::table('assign', function (Blueprint $table) {
            //
        });
    }
}
