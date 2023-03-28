<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersDropReturnedProductAddProductWeight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('returned_product_quantity');
            $table->dropColumn('returned_product_remark');
            $table->unsignedInteger('product_weight')->default(0)->comment('商品重量')->after('factory_product_sequence_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('product_weight');
            $table->unsignedInteger('returned_product_quantity')->default(0)->comment('返品数')->after('factory_cancel_flag');
            $table->string('returned_product_remark', 50)->default('')->comment('返品備考')->after('returned_product_quantity');
        });
    }
}
