<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_allocations', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->date('harvesting_date')->comment('収穫日');
            $table->string('order_number', 14)->comment('Fsystem注文番号');
            $table->unsignedInteger('allocation_quantity')->comment('引当数量');
            $table->string('last_allocated_by', 15)->comment('最終引当者');
            $table->datetime('last_allocated_at')->comment('最終引当日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'species_code', 'harvesting_date', 'order_number'], 'product_allocations_primary_key');
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
        Schema::dropIfExists('product_allocations');
    }
}
