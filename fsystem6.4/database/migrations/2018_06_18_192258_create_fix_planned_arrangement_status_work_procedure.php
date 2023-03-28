<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixPlannedArrangementStatusWorkProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(file_get_contents(__DIR__."/../procedure/fix_planned_arrangement_status_work.sql"));
        DB::unprepared(file_get_contents(__DIR__."/../procedure/fix_planned_arrangement_detail_status_work.sql"));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP procedure IF EXISTS fix_planned_arrangement_detail_status_work;');
        DB::unprepared('DROP procedure IF EXISTS fix_planned_arrangement_status_work;');
    }
}
