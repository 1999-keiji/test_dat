<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductFactoryProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_products', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedInteger('sequence_number')->comment('連番');
            $table->string('product_code', 15)->comment('商品コード');
            $table->string('factory_product_name', 50)->default('')->comment('工場商品名');
            $table->string('factory_product_abbreviation', 15)->default('')->comment('工場商品略称');
            $table->unsignedSmallInteger('number_of_heads')->default(0)->comment('基本入り株数');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->unsignedSmallInteger('number_of_cases')->default(0)->comment('ケース入数');
            $table->string('unit', 5)->default('')->comment('単位');
            $table->string('remark', 100)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'sequence_number'], 'factory_products_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('product_code')->references('product_code')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_products');
    }
}
