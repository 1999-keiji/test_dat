<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedInteger('sequence_number')->comment('連番');
            $table->string('screen', 1)->comment('画面');
            $table->string('warehouse_code', 15)->nullable()->comment('倉庫コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->string('stock_status', 1)->default(1)->comment('状態');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り数株');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->datetime('harvesting_date')->comment('収穫日');
            $table->datetime('expiration_date')->nullable()->comment('有効期限');
            $table->datetime('delivery_date')->nullable()->comment('納期');
            $table->string('allcate', 1)->default(1)->comment('引当');
            $table->string('delivery_destination_code', 10)->default(null)->nullable()->comment('納入先コード');
            $table->unsignedTinyInteger('delivery_lead_time')->nullable()->comment('配送リードタイム');
            $table->Integer('stock_quantity')->nullable()->comment('在庫数量');
            $table->Integer('transistion_quantity')->default(0)->comment('遷移数量');
            $table->string('stock_id')->comment('在庫id');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');
            $table->primary(['factory_code', 'sequence_number'], 'stock_histories_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_histories');
    }
}
