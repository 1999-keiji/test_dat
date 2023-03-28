<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesDropColumnReturnedProductQuantityAndReturnedProductRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->dropColumn(['returned_product_quantity', 'returned_product_remark']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->unsignedInteger('returned_product_quantity')
                ->default(0)
                ->comment('返品数')
                ->after('factory_cancel_flag');
            $table->string('returned_product_remark', 50)
                ->default('')
                ->comment('返品備考')
                ->after('returned_product_quantity');
        });
    }
}
