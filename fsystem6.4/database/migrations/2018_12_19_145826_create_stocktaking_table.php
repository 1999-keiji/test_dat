<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocktakingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktaking', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->datetime('stocktaking_date')->nullable()->comment('棚卸年月');
            $table->datetime('stocktaking_comp_at')->nullable()->comment('棚卸完了日');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');
            $table->primary(['factory_code', 'stocktaking_date'], 'stocktaking_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocktaking');
    }
}
