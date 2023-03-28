<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoryWarehousesAddColumnPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_warehouses', function (Blueprint $table) {
            $table->dropColumn('is_default_destination');
            $table->unsignedTinyInteger('priority')->comment('優先度')->after('warehouse_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factory_warehouses', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->boolean('is_default_destination')->default(false)->comment('デフォルト商品搬出先')->after('warehouse_code');
        });
    }
}
