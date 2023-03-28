<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePanelStateAddIndexFactoryCodeAndNextGrowthStageDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('panel_state', function (Blueprint $table) {
            $table->index(['factory_code', 'next_growth_stage_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('panel_state', function (Blueprint $table) {
            $table->dropIndex(['factory_code', 'next_growth_stage_date']);
        });
    }
}
