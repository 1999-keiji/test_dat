<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_states', function (Blueprint $table) {
            $table->increments('stock_id')->comment('在庫コード');
            $table->date('stock_date')->comment('日付');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->datetime('harvesting_date')->comment('収穫日');
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り数株');
            $table->unsignedSmallInteger('weight_per_number_of_heads')->default(0)->comment('基本入り数あたり重量');
            $table->string('input_group', 1)->default('')->comment('出来高入力グループ');
            $table->string('delivery_destination_code', 10)->nullable()->comment('納入先コード');
            $table->date('delivery_date')->nullable()->comment('納期');
            $table->integer('stock_quantity')->nullable()->comment('在庫数量');
            $table->unsignedSmallInteger('stock_weight')->default(0)->comment('在庫重量');
            $table->integer('stock_number')->nullable()->comment('在庫株数');
            $table->Integer('disposal_quantity')->default(0)->nullable()->comment('廃棄数量');
            $table->unsignedSmallInteger('disposal_weight')->default(0)->comment('廃棄重量');
            $table->string('stock_status', 1)->default(1)->nullable()->comment('状態');
            $table->unsignedInteger('factory_product_sequence_number')->nullable()->comment('工場商品連番');
            $table->unsignedSmallInteger('number_of_cases')->default(0)->nullable()->comment('ケース入数');
            $table->date('shipping_date')->nullable()->comment('出荷日');
            $table->datetime('fixed_shipping_at')->nullable()->comment('出荷確定日時');
            $table->unsignedTinyInteger('delivery_lead_time')->nullable()->comment('配送リードタイム');
            $table->date('expiration_date')->nullable()->comment('有効期限');
            $table->string('before_warehouse_code', 15)->nullable()->comment('移動元倉庫コード');
            $table->date('moving_start_at')->nullable()->comment('移動開始日');
            $table->date('moving_complete_at')->nullable()->comment('移動完了日');
            $table->string('factory_abbreviation', 20)->nullable()->comment('工場略称');
            $table->string('warehouse_abbreviation', 20)->nullable()->comment('倉庫略称');
            $table->string('species_abbreviation', 10)->nullable()->comment('品種略称');
            $table->string('delivery_destination_abbreviation', 50)->nullable()->comment('納入先略称');
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
        Schema::dropIfExists('stock_states');
    }
}
