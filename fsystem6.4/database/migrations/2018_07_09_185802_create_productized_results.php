<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductizedResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productized_results', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->date('harvesting_date')->comment('収穫日');
            $table->unsignedInteger('triming')->default(0)->comment('トリミング');
            $table->unsignedInteger('product_failure')->default(0)->comment('障害品');
            $table->unsignedInteger('packing')->default(0)->comment('パッキング');
            $table->unsignedInteger('crop_failure')->default(0)->comment('収穫不良');
            $table->unsignedInteger('sample')->default(0)->comment('検査サンプル');
            $table->unsignedInteger('weight_of_discarded')->default(0)->comment('廃棄重量');
            $table->datetime('fixed_at')->comment('確定日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'species_code', 'harvesting_date'], 'productized_results_primary_key');
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
        Schema::dropIfExists('productized_results');
    }
}
