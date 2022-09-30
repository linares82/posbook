<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMovementIdToLnCashBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ln_cash_boxes', function (Blueprint $table) {
            $table->bigInteger('movement_id')->unsigned()->nullable();
            $table->index('movement_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ln_cash_boxes', function (Blueprint $table) {
            //
        });
    }
}
