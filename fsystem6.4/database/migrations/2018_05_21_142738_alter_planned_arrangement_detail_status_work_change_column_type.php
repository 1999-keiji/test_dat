<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlannedArrangementDetailStatusWorkChangeColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (range(1, 50) as $panel) {
            DB::statement("ALTER TABLE `planned_arrangement_detail_status_work` MODIFY `panel_status_{$panel}` TINYINT UNSIGNED COMMENT 'パネル状況{$panel}'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (range(1, 50) as $panel) {
            DB::statement("ALTER TABLE `planned_arrangement_detail_status_work` MODIFY `panel_status_{$panel}` VARCHAR(2) COMMENT 'パネル状況{$panel}'");
        }
    }
}
