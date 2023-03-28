<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoriesSetDefaultValueCollectionGuideMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->string('collection_guide_message', 255)->default('')->comment('集荷案内メッセージ1')->change();
            $table->string('collection_guide_message2', 255)->default('')->comment('集荷案内メッセージ2')->change();
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
            $table->string('collection_guide_message', 255)->comment('集荷案内メッセージ1')->change();
            $table->string('collection_guide_message2', 255)->comment('集荷案内メッセージ2')->change();
        });
    }
}
