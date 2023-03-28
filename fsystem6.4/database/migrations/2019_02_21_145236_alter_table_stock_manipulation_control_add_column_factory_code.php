<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockManipulationControlAddColumnFactoryCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_manipulation_control', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード')->first();
            $table->primary('factory_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_manipulation_control', function (Blueprint $table) {
            $table->dropPrimary('stock_manipulation_control_factory_code_primary');
            $table->dropColumn('factory_code');
        });
    }
}
