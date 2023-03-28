<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFactoryCyclePatternItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_cycle_pattern_items', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedTinyInteger('cycle_pattern_sequence_number')->comment('サイクルパターン連番');
            $table->string('pattern', 1)->comment('パターン');
            $table->unsignedTinyInteger('day_of_the_week')->comment('曜日');
            $table->unsignedTinyInteger('number_of_beds')->comment('移動ベッド数');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'cycle_pattern_sequence_number', 'pattern', 'day_of_the_week'], 'factory_cycle_pattern_items_primary');
            $table->foreign(['factory_code', 'cycle_pattern_sequence_number'], 'factory_cycle_patterns_foreign')->references(['factory_code', 'sequence_number'])->on('factory_cycle_patterns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_cycle_pattern_items');
    }
}
