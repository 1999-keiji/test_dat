<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryFactoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_factory_products', function (Blueprint $table) {
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedInteger('factory_product_sequence_number')->comment('工場商品連番');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number'], 'delivery_factory_products_primary');
            $table->foreign('delivery_destination_code')->references('delivery_destination_code')->on('delivery_destinations');
            $table->foreign(['factory_code', 'factory_product_sequence_number'], 'delivery_factory_products_factory_products_foreign')->references(['factory_code', 'sequence_number'])->on('factory_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_factory_products');
    }
}
