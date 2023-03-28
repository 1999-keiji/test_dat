<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReturnedProductsAddColumnStockId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returned_products', function (Blueprint $table) {
            $table->integer('stock_id')->nullable()->comment('在庫ID')->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returned_products', function (Blueprint $table) {
            $table->dropColumn('stock_id');
        });
    }
}
