<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockManipulationControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_manipulation_control', function (Blueprint $table) {
            $table->boolean('stock_control_flag')->default(false)->comment('在庫操作制御フラグ');
            $table->datetime('control_start_at')->nullable()->comment('制御開始日時');
            $table->datetime('control_comp_at')->nullable()->comment('制御完了日時');
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
        Schema::dropIfExists('stock_manipulation_control');
    }
}
