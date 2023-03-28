<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultDetailsAddColumnWarehouseCodeAndAdjustmentQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_result_details', function (Blueprint $table) {
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->Integer('adjustment_quantity')->default(0)->nullable()->comment('調整在庫');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productized_result_details', function (Blueprint $table) {
            $table->dropColumn('warehouse_code');
            $table->dropColumn('adjustment_quantity');
        });
    }
}
