<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockStatesModifyColumnsStockWeightAndDisposalWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->unsignedInteger('stock_weight')->default(0)->comment('在庫重量')->change();
            $table->unsignedInteger('disposal_weight')->default(0)->comment('廃棄重量')->change();
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
            $table->unsignedSmallInteger('stock_weight')->default(0)->comment('在庫重量');
            $table->unsignedSmallInteger('disposal_weight')->default(0)->comment('廃棄重量');
        });
    }
}
