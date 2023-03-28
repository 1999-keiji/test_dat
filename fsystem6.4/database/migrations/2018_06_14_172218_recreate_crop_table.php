<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateCropTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('crop');

        Schema::create('crop', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り株数');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->date('date')->comment('日付');
            $table->unsignedInteger('crop_number')->comment('出来高数');
            $table->unsignedInteger('crop_stock_number')->comment('出来高株数');
            $table->unsignedDecimal('product_rate', 5, 2)->comment('製品化率');
            $table->unsignedInteger('product_weight')->comment('製品化重量');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group', 'date'], 'crop_primary_key');
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
        Schema::dropIfExists('crop');

        Schema::create('crop', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('factory_product_sequence_number')->comment('工場商品連番');
            $table->date('date')->comment('日付');
            $table->unsignedInteger('crop_number')->comment('出来高数');
            $table->unsignedInteger('crop_stock_number')->comment('出来高株数');
            $table->unsignedDecimal('product_rate', 5, 2)->comment('製品化率');
            $table->unsignedInteger('product_weight')->comment('製品化重量');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'factory_product_sequence_number', 'date'], 'crop_primary_key');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
        });
    }
}
