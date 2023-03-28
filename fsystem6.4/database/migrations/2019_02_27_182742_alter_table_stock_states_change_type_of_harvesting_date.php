<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockStatesChangeTypeOfHarvestingDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->date('harvesting_date')->comment('収穫日')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->datetime('harvesting_date')->comment('収穫日')->change();
        });
    }
}
