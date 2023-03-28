<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryProductSpecialPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_product_special_prices', function (Blueprint $table) {
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedInteger('factory_product_sequence_number')->comment('工場商品連番');
            $table->string('currency_code', 3)->comment('通貨コード');
            $table->date('application_started_on')->comment('適用開始日');
            $table->date('application_ended_on')->comment('適用終了日');
            $table->unsignedDecimal('unit_price', 14, 5)->default(0.00000)->comment('単価');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number', 'currency_code', 'application_started_on'], 'factory_product_special_prices_primary');
            $table->foreign(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number'], 'delivery_factory_products_foreign')->references(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number'])->on('delivery_factory_products');
            $table->foreign('currency_code')->references('currency_code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_product_special_prices');
    }
}
