<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlannedCultivationStatusWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planned_cultivation_status_work', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('simulation_id')->comment('シミュレーションID');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->unsignedTinyInteger('display_kubun')->comment('表示区分');
            $table->unsignedTinyInteger('growing_stages_sequence_number')->comment('生育ステージ連番');
            $table->date('date')->comment('日付');

            $table->unsignedTinyInteger('weekday')->comment('曜日');
            $table->unsignedSmallInteger('number_of_holes')->comment('パネル穴数');
            $table->smallInteger('bed_number')->comment('設置ベッド数');
            $table->unsignedTinyInteger('patterns_number')->comment('パターン数');
            $table->unsignedTinyInteger('floor_number')->comment('フロア数');

            for($i=1;$i<=10;$i++) {
                $table->smallInteger('moving_panel_count_pattern_'.$i)->nullable()->comment('移動パネル数パターン'.$i);
            }

            for($i=1;$i<=10;$i++) {
                $table->smallInteger('moving_bed_count_floor_'.$i.'_sum')->nullable()->comment('移動ベッド数フロア'.$i.'合計');
                for($j=1;$j<=10;$j++) {
                    $table->smallInteger('moving_bed_count_floor_'.$i.'_pattern_'.$j)->nullable()->comment('移動ベッド数フロア'.$i.'パターン'.$j);
                }
            }

            $table->datetime('fixed_at')->nullable()->comment('確定日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'simulation_id', 'factory_species_code', 'display_kubun', 'growing_stages_sequence_number', 'date'], 'planned_cultivation_status_work_primary_key');
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
        Schema::dropIfExists('planned_cultivation_status_work');
    }
}
