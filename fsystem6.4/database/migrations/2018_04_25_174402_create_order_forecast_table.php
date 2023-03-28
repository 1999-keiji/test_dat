<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderForecastTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_forecast', function (Blueprint $table){
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedInteger('factory_product_sequence_number')->comment('工場商品連番');
            $table->date('date')->comment('日付');
            $table->date('harvesting_date')->comment('収穫日');
            $table->date('shipping_date')->comment('出荷日');
            $table->unsignedInteger('forecast_number')->comment('商品数');
            $table->unsignedInteger('forecast_weight')->comment('商品重量');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['delivery_destination_code', 'factory_code', 'factory_product_sequence_number', 'date'], 'order_forecast_primary_key');
            $table->foreign('delivery_destination_code')->references('delivery_destination_code')->on('delivery_destinations');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            // $table->foreign('factory_product_sequence_number')->references('sequence_number')->on('factory_products');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_forecast');
    }
}
