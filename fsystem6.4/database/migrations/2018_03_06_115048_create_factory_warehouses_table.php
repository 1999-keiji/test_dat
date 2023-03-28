<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_warehouses', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('warehouse_code', 15)->comment('倉庫コード');
            $table->boolean('is_default_destination')->default(false)->comment('デフォルト商品搬出先');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'warehouse_code']);
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('warehouse_code')->references('warehouse_code')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_warehouses');
    }
}
