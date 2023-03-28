<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderingDetailsAddSequenceNumberAndFsystemSharingFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordering_details', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->comment('連番')->first();
            $table->boolean('fsystem_sharing_flag')->default(false)->nullable()->comment('Fsystem連携フラグ')->after('sequence_number');
            $table->dropPrimary('ordering_details_primary_key');
            $table->primary(['sequence_number', 'own_company_code', 'place_order_number', 'place_order_chapter_number'], 'ordering_details_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordering_details', function (Blueprint $table) {
            $table->dropPrimary('ordering_details_primary_key');
            $table->dropColumn('sequence_number');
            $table->dropColumn('fsystem_sharing_flag');
            $table->primary(['own_company_code', 'place_order_number', 'place_order_chapter_number'], 'ordering_details_primary_key');
        });
    }
}
