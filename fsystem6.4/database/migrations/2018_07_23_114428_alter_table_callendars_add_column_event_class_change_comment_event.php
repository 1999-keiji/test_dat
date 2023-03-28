<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCallendarsAddColumnEventClassChangeCommentEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('callendars', function (Blueprint $table) {
            $table->string('event_class', 1)->comment('行事区分')->after('date');
        });
        Schema::table('callendars', function (Blueprint $table) {
            $table->string('event',30)->comment('行事')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('event_class');
        Schema::table('callendars', function (Blueprint $table) {
            $table->string('event',30)->comment('登録日時')->change();
        });
    }
}
