<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCropTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
        Schema::dropIfExists('crop');
    }
}
