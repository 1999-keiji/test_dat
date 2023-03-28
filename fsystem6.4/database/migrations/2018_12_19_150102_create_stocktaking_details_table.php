<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocktakingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktaking_details', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->datetime('stocktaking_date')->comment('棚卸年月');
            $table->string('species_code', 15)->comment('品種コード');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り数株');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->smallInteger('number_of_cases')->default(0)->comment('ケース入数');
            $table->string('delivery_destination_code', 10)->nullable()->comment('納入先コード');
            $table->Integer('stock_quantity')->comment('在庫数量');
            $table->string('unit', 5)->default('')->comment('単位');
            $table->Integer('actual_stock_quantity')->comment('実在庫数');
            $table->string('remark', 255)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary([
                'factory_code',
                'warehouse_code',
                'stocktaking_date',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                'number_of_cases'
            ], 'stocktaking_details_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocktaking_details');
    }
}
