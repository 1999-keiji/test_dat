<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoriesAddColumnCollectionGuideStaffAndCollectionGuideMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->string('collection_guide_staff', 50)->comment('集荷案内担当者')->after('symbolic_code');
            $table->string('collection_guide_message', 255)->comment('集荷案内メッセージ1')->after('collection_guide_staff');
            $table->string('collection_guide_message2', 255)->comment('集荷案内メッセージ2')->after('collection_guide_message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->dropColumn('collection_guide_staff');
            $table->dropColumn('collection_guide_message');
            $table->dropColumn('collection_guide_message2');
        });
    }
}
