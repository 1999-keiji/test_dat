<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransportCompaniesAddColumnCanTransportDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_companies', function (Blueprint $table) {
            $table->boolean('can_transport_double')->default(false)->comment('ニコイチ配送可否')->after('mail_address');
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
            $table->dropColumn('can_transport_double');
        });
    }
}
