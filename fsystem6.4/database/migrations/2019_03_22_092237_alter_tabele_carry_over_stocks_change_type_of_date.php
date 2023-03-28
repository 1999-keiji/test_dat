<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTabeleCarryOverStocksChangeTypeOfDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carry_over_stocks', function (Blueprint $table) {
            $table->date('date')->comment('日付')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carry_over_stocks', function (Blueprint $table) {
            $table->datetime('date')->comment('日付')->change();
        });
    }
}
