<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTranspoteCompaniesRenameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transpote_companies',function($table){
            $table->renameColumn('company_name', 'transpote_company_name');
            $table->renameColumn('branch_bane', 'transpote_branch_bane');
            $table->renameColumn('company_abbreviation', 'transpote_company_abbreviation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transpote_companies',function($table){
            $table->renameColumn('transpote_company_name', 'company_name');
            $table->renameColumn('transpote_branch_bane', 'branch_bane');
            $table->renameColumn('transpote_company_abbreviation', 'company_abbreviation');
        });
    }
}
