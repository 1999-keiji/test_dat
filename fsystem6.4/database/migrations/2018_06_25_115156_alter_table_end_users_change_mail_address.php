<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableEndUsersChangeMailAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('end_users', function (Blueprint $table) {
            $table->string('mail_address', 250)->default('')->comment('メールアドレス')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('end_users', function (Blueprint $table) {
            $table->string('mail_address', 30)->default('')->comment('メールアドレス')->change();
        });
    }
}
