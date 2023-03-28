<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePlannedCultivationStatusWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `planned_cultivation_status_work` CHANGE COLUMN `weekday` `day_of_the_week` TINYINT UNSIGNED NOT NULL COMMENT '曜日'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `planned_cultivation_status_work` CHANGE COLUMN `day_of_the_week` `weekday` TINYINT UNSIGNED NOT NULL COMMENT '曜日'");
    }
}
