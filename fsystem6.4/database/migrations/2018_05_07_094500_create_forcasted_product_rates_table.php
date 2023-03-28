<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForcastedProductRatesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forcasted_product_rates', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->date('date')->comment('日付');
            $table->unsignedDecimal('product_rate', 5, 2)->comment('製品化率');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'species_code', 'date'], 'forcasted_product_rate_primary_key');
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
        Schema::dropIfExists('forcasted_product_rates');
    }
}
