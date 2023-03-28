<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCultivationStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cultivation_states', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->date('start_of_week')->comment('作業週初日');
            $table->unsignedTinyInteger('growing_stage_sequence_number')->comment('生育ステージ連番');
            $table->date('working_date')->comment('作業日');
            $table->unsignedTinyInteger('day_of_the_week')->comment('曜日');
            $table->unsignedSmallInteger('number_of_holes')->comment('パネル穴数');
            $table->unsignedTinyInteger('patterns_number')->comment('パターン数');
            $table->unsignedTinyInteger('floor_number')->comment('フロア数');

            for ($i = 1; $i <= 10; $i++) {
                $table->smallInteger('moving_panel_count_pattern_'.$i)->nullable()->comment('移動パネル数パターン'.$i);
            }

            for ($i = 1; $i <= 10; $i++) {
                $table->smallInteger('moving_bed_count_floor_'.$i.'_sum')->nullable()->comment('移動ベッド数フロア'.$i.'合計');
                for ($j = 1; $j <= 10; $j++) {
                    $table->smallInteger('moving_bed_count_floor_'.$i.'_pattern_'.$j)->nullable()->comment('移動ベッド数フロア'.$i.'パターン'.$j);
                }
            }

            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary([
                'factory_code',
                'factory_species_code',
                'start_of_week',
                'growing_stage_sequence_number',
                'working_date'
            ], 'cultivation_states_primary_key');

            $table->foreign([
                'factory_code',
                'factory_species_code',
                'start_of_week'
            ], 'bed_states_foreign_cultivation_states')
                ->references(['factory_code', 'factory_species_code', 'start_of_week'])
                ->on('bed_states');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cultivation_states');
    }
}
