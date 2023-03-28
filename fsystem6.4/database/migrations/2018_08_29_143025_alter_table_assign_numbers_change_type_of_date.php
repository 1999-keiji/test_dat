<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAssignNumbersChangeTypeOfDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_numbers', function (Blueprint $table) {
            $table->date('date')->comment('年月日')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_numbers', function (Blueprint $table) {
            $table->dateTime('date')->comment('年月日')->change();
        });
    }
}
