<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockResulByWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_result_by_warehouses', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->date('harvesting_date')->comment('収穫日');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り数株');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->Integer('product_stock_quantity')->default(0)->comment('製品化在庫数量');
            $table->Integer('adjustment_quantity')->default(0)->comment('調整数量');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary([
                'factory_code',
                'species_code',
                'harvesting_date',
                'warehouse_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
            ], 'stock_resul_by_warehouses_primary_key');

            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('species_code')->references('species_code')->on('species');
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
        Schema::dropIfExists('stock_result_by_warehouses');
    }
}
