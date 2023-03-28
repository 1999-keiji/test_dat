<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePanelStateAddIndexToTuneUpBatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            ALTER TABLE panel_state
                ADD INDEX panel_state_factory_code_bed_index (factory_code, bed_row, bed_column),
                ADD INDEX panel_state_factory_code_bed_date_index (factory_code, bed_row, bed_column, date),
                ADD INDEX panel_state_factory_code_date_index (factory_code, date);
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE panel_state DROP INDEX panel_state_factory_code_bed_index;');
        DB::statement('ALTER TABLE panel_state DROP INDEX panel_state_factory_code_bed_date_index;');
        DB::statement('ALTER TABLE panel_state DROP INDEX panel_state_factory_code_date_index;');
    }
}
