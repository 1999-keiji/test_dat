<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePanelStateAddIndexFactoryCodeFactorySpeciesCodeStatgeStartDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('panel_state', function (Blueprint $table) {
            $table->index(
                ['factory_code', 'factory_species_code', 'stage_start_date'],
                'panel_state_factory_code_stage_start_date_index'
            );
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
            $table->dropIndex('panel_state_factory_code_stage_start_date_index');
        });
    }
}
