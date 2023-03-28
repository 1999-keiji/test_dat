<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableEndUsersModifyCompanyGroupCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('end_users', function (Blueprint $table) {
            $table->string('company_group_code', 8)->default('')->comment('企業グループコード')->change();
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
            $table->string('company_group_code', 6)->default('')->comment('企業グループコード')->change();
        });
    }
}
