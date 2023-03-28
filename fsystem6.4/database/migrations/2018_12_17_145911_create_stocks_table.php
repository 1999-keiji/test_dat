<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('stock_id')->comment('在庫コード');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->datetime('harvesting_date')->comment('収穫日');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り数株');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->Integer('stock_quantity')->nullable()->comment('在庫数量');
            $table->unsignedSmallInteger('stock_weight')->default(0)->comment('在庫重量');
            $table->Integer('disposal_quantity')->default(0)->nullable()->comment('廃棄数量');
            $table->smallInteger('disposal_weight')->default(0)->comment('廃棄重量');
            $table->datetime('disposal_at')->nullable()->comment('廃棄日');
            $table->string('disposal_remark', 30)->nullable()->comment('廃棄備考');
            $table->string('stock_status', 1)->default(1)->nullable()->comment('状態');
            $table->string('delivery_destination_code', 10)->nullable()->comment('納入先コード');
            $table->string('before_warehouse_code', 15)->nullable()->comment('移動元倉庫コード');
            $table->datetime('movig_start_at')->nullable()->comment('移動開始日');
            $table->datetime('movig_comprate_at')->nullable()->comment('移動完了日');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
