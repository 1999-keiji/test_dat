<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateReplicationPanelStateProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP procedure IF EXISTS `replication_panel_state`');
        DB::unprepared(file_get_contents(database_path('procedure/replication_panel_state_v2.sql')));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(file_get_contents(database_path('procedure/replication_panel_state.sql')));
    }
}
