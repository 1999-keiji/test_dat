<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultDetailsDropColumnStockWeightAdjustmentQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_result_details', function (Blueprint $table) {
            $table->dropColumn('adjustment_quantity');
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
            $table->Integer('adjustment_quantity')->default(0)->comment('調整在庫')->after('product_quantity');
        });
    }
}
