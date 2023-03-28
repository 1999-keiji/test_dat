<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePlannedArrangementDetailStatusWorkAddPanelColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planned_arrangement_detail_status_work', function (Blueprint $table) {
            foreach (range(51, 100) as $panel) {
                $prev_panel = $panel - 1;
                $table->unsignedTinyInteger("panel_status_{$panel}")->nullable()->comment("パネル状況{$panel}")->after("panel_status_{$prev_panel}");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planned_arrangement_detail_status_work', function (Blueprint $table) {
            foreach (range(51, 100) as $panel) {
                $table->dropColumn("panel_status_{$panel}");
            }
        });
    }
}
