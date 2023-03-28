<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTranspoteCompaniesRenameTranspoteBranchName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transpote_companies', function (Blueprint $table) {
            $table->renameColumn('transpote_branch_bane', 'transpote_branch_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transpote_companies', function (Blueprint $table) {
            $table->renameColumn('transpote_branch_name', 'transpote_branch_bane');
        });
    }
}
