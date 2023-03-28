<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoryCyclePatternItemsRenameColumnToNumberOfPanels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `factory_cycle_pattern_items` CHANGE COLUMN `number_of_beds` `number_of_panels` TINYINT UNSIGNED NOT NULL COMMENT '移動パネル数'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `factory_cycle_pattern_items` CHANGE COLUMN `number_of_panels` `number_of_beds` TINYINT UNSIGNED NOT NULL COMMENT '移動ベッド数'");
    }
}
