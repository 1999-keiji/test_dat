<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeliveryDestinationsAddColumnTranspoteCompanyCodeAndCollectionTimeSequenceNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->string('transpote_company_code', 15)->default('')->comment('運送会社コード')->after('cii_company_code');
            $table->unsignedInteger('collection_time_sequence_number')->default(1)->comment('集荷時間連番')->after('transpote_company_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->dropColumn('collection_time_sequence_number');
            $table->dropColumn('transpote_company_code');
        });
    }
}
