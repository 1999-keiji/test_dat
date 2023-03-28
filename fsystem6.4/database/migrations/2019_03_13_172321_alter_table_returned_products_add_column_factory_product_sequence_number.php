<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReturnedProductsAddColumnFactoryProductSequenceNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returned_products', function (Blueprint $table) {
            $table->unsignedInteger('factory_product_sequence_number')->comment('工場商品連番')->after('product_code');
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
            $table->dropColumn('factory_product_sequence_number');
        });
    }
}
