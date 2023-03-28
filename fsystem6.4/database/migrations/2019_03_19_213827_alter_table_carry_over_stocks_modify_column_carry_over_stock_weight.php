<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCarryOverStocksModifyColumnCarryOverStockWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carry_over_stocks', function (Blueprint $table) {
            $table->unsignedInteger('carry_over_stock_weight')->default(0)->comment('繰越在庫重量')->change();
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
            $table->smallInteger('carry_over_stock_weight')->default(0)->comment('繰越在庫重量');
        });
    }
}
