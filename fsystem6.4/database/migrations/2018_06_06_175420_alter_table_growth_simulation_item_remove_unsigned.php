<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableGrowthSimulationItemRemoveUnsigned extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('growth_simulation_item', function (Blueprint $table) {
            $table->smallInteger('bed_number')->nullable()->comment('ベッド数')->change();
            $table->smallInteger('panel_number')->nullable()->comment('パネル数')->change();
            $table->integer('stock_number')->nullable()->comment('株数')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('growth_simulation_item', function (Blueprint $table) {
            $table->unsignedSmallInteger('bed_number')->nullable()->comment('ベッド数')->change();
            $table->unsignedSmallInteger('panel_number')->nullable()->comment('パネル数')->change();
            $table->unsignedInteger('stock_number')->nullable()->comment('株数')->change();
        });
    }
}
