<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultDetailsDropStockQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_result_details', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
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
            $table->unsignedInteger('stock_quantity')->comment('在庫数量')->after('product_quantity');
        });
    }
}
