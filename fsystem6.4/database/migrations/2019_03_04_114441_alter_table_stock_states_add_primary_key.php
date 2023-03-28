<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockStatesAddPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->integer('stock_id')->comment('在庫コード')->change();
            $table->dropPrimary(['stock_id']);
            $table->primary([
                'stock_id',
                'stock_date'
            ], 'stock_states_primary_keys');
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
            $table->increments('stock_id')->comment('在庫コード')->change();
        });
    }
}
