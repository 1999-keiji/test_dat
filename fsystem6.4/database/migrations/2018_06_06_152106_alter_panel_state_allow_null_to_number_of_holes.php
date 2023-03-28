<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPanelStateAllowNullToNumberOfHoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('panel_state', function (Blueprint $table) {
            $table->unsignedSmallInteger('number_of_holes')->nullable()->comment('穴数')->change();
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
            $table->unsignedSmallInteger('number_of_holes')->comment('穴数')->change();
        });
    }
}
