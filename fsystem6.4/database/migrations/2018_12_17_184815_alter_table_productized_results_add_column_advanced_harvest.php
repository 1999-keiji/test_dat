<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultsAddColumnAdvancedHarvest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_results', function (Blueprint $table) {
            $table->integer('crop_failure')->default(0)->comment('収穫廃棄')->change();
            $table->integer('advanced_harvest')->default(0)->comment('前採り')->after('sample');
        });

        DB::statement('UPDATE `productized_results` SET `crop_failure` = `crop_failure` * -1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('UPDATE `productized_results` SET `crop_failure` = `crop_failure` * -1');

        Schema::table('productized_results', function (Blueprint $table) {
            $table->unsignedInteger('crop_failure')->default(0)->comment('収穫廃棄')->change();
            $table->dropColumn('advanced_harvest');
        });
    }
}
