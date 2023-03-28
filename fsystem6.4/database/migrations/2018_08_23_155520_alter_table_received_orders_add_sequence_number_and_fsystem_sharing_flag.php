<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReceivedOrdersAddSequenceNumberAndFsystemSharingFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('received_orders', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->comment('連番')->first();
            $table->boolean('fsystem_sharing_flag')->default(false)->nullable()->comment('Fsystem連携フラグ')->after('sequence_number');
            $table->primary('sequence_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('received_orders', function (Blueprint $table) {
            $table->dropPrimary('sequence_number');
            $table->dropColumn('sequence_number');
            $table->dropColumn('fsystem_sharing_flag');
        });
    }
}
