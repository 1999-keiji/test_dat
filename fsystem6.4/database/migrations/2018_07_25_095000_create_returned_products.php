<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('returned_products', function (Blueprint $table) {
            $table->string('order_number', 14)->comment('Fsystem注文番号');
            $table->datetime('returned_on')->comment('返品日');
            $table->string('product_code', 15)->comment('商品コード');
            $table->unsignedDecimal('unit_price', 14, 5)->comment('単価');
            $table->unsignedInteger('quantity')->default(0)->comment('返品数');
            $table->string('remark', 50)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('returned_products');
    }
}
