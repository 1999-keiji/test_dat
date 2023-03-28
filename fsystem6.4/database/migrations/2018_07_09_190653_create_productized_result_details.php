<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductizedResultDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productized_result_details', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->date('harvesting_date')->comment('収穫日');
            $table->unsignedDecimal('number_of_heads', 6, 1)->comment('基本入り株数');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default(0)->comment('出来高入力グループ');
            $table->unsignedInteger('product_quantity')->comment('製品化数量');
            $table->unsignedInteger('stock_quantity')->comment('在庫数量');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'species_code', 'harvesting_date', 'number_of_heads', 'input_group', 'weight_per_number_of_heads'], 'productized_results_detail_primary_key');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('species_code')->references('species_code')->on('species');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productized_result_details');
    }
}
