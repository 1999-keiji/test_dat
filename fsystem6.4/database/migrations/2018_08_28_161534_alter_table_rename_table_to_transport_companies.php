<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRenameTableToTransportCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('transpote_companies', 'transport_companies');

        Schema::table('transport_companies', function (Blueprint $table) {
            $table->renameColumn('transpote_company_code', 'transport_company_code');
            $table->renameColumn('transpote_company_name', 'transport_company_name');
            $table->renameColumn('transpote_branch_name', 'transport_branch_name');
            $table->renameColumn('transpote_company_abbreviation', 'transport_company_abbreviation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transport_companies', function (Blueprint $table) {
            $table->renameColumn('transport_company_code', 'transpote_company_code');
            $table->renameColumn('transport_company_name', 'transpote_company_name');
            $table->renameColumn('transport_branch_name', 'transpote_branch_name');
            $table->renameColumn('transport_company_abbreviation', 'transpote_company_abbreviation');
        });

        Schema::rename('transport_companies', 'transpote_companies');
    }
}
