<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderingsAddSequenceNumberAndFsystemSharingFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orderings', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->comment('連番')->first();
            $table->boolean('fsystem_sharing_flag')->default(false)->nullable()->comment('Fsystem連携フラグ')->after('sequence_number');
            $table->primary(['sequence_number', 'place_order_number'], 'orderings_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orderings', function (Blueprint $table) {
            $table->dropPrimary('orderins_primary_key');
            $table->dropColumn('sequence_number');
            $table->dropColumn('fsystem_sharing_flag');
        });
    }
}
