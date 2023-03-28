<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCallendarsChangeEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('callendars', function (Blueprint $table) {
            $table->string('event',30)->comment('登録日時')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('callendars', function (Blueprint $table) {
            $table->unsignedInteger('event')->comment('登録日時')->change();
        });
    }
}
