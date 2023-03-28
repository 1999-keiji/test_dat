<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_warehouses', function (Blueprint $table) {
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->unsignedTinyInteger('delivery_lead_time')->nullable()->comment('配送リードタイム');
            $table->unsignedTinyInteger('shipment_lead_time')->default()->comment('出荷リードタイム');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['delivery_destination_code', 'warehouse_code'], 'delivery_warehouses_primary');
            $table->foreign('delivery_destination_code')->references('delivery_destination_code')->on('delivery_destinations');
            $table->foreign('warehouse_code')->references('warehouse_code')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_warehouses');
    }
}
