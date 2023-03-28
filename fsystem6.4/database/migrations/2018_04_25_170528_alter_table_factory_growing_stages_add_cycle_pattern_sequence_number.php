<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoryGrowingStagesAddCyclePatternSequenceNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_growing_stages', function (Blueprint $table) {
            $table->unsignedTinyInteger('cycle_pattern_sequence_number')->nullable()->comment('サイクルパターン連番')->after('yield_rate');
            $table->foreign(['factory_code', 'cycle_pattern_sequence_number'], 'factory_cycle_patterns_factory_growing_stages_foreign')->references(['factory_code', 'sequence_number'])->on('factory_cycle_patterns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factory_growing_stages', function (Blueprint $table) {
            $table->dropForeign('factory_cycle_patterns_factory_growing_stages_foreign');
            $table->dropColumn('cycle_pattern_sequence_number');
        });
    }
}
