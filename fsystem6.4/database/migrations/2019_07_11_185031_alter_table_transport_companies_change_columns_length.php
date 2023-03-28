<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransportCompaniesChangeColumnsLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_companies', function (Blueprint $table) {
            $table->string('transport_company_name', 50)->default('')->comment('運送会社名')->change();
            $table->string('transport_branch_name', 50)->default('')->comment('運送会社支店名')->change();
            $table->string('transport_company_abbreviation', 20)->default('')->comment('運送会社略称')->change();
            $table->string('mail_address', 250)->default('')->comment('メールアドレス')->change();
            $table->string('note', 50)->default('')->comment('注意事項')->change();
            $table->string('remark', 255)->default('')->comment('注意事項')->change();
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
            $table->string('transport_company_name', 20)->default('')->comment('運送会社名')->change();
            $table->string('transport_branch_name', 10)->default('')->comment('運送会社支店名')->change();
            $table->string('transport_company_abbreviation', 50)->default('')->comment('運送会社略称')->change();
            $table->string('mail_address', 30)->default('')->comment('メールアドレス')->change();
            $table->string('note', 30)->default('')->comment('注意事項')->change();
            $table->string('remark', 30)->default('')->comment('注意事項')->change();
        });
    }
}
