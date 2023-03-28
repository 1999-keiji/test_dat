<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoryCyclePatternsChangePrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_cycle_patterns', function (Blueprint $table) {
            $table->dropForeign(['factory_code']);
            $table->dropPrimary('factory_cycle_patterns_primary');
            $table->dropColumn(['pattern', 'day_of_the_week', 'number_of_beds']);

            $table->unsignedTinyInteger('sequence_number')->comment('連番')->after('factory_code');
            $table->string('cycle_pattern_name', 50)->default('')->comment('サイクルパターン名')->after('sequence_number');

            $table->primary(['factory_code', 'sequence_number'], 'factory_cycle_patterns_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factory_cycle_patterns', function (Blueprint $table) {
            $table->dropForeign(['factory_code']);
            $table->dropPrimary('factory_cycle_patterns_primary');
            $table->dropColumn(['sequence_number', 'cycle_pattern_name']);

            $table->string('pattern', 1)->comment('パターン')->after('factory_code');
            $table->unsignedTinyInteger('day_of_the_week')->comment('曜日')->after('pattern');
            $table->unsignedTinyInteger('number_of_beds')->comment('移動ベッド数')->after('day_of_the_week');

            $table->primary(['factory_code', 'pattern', 'day_of_the_week'], 'factory_cycle_patterns_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
        });
    }
}
