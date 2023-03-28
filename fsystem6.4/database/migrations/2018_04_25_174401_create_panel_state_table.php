<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePanelStateTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('panel_state', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedBigInteger('panel_id')->comment('パネルID');
            $table->date('date')->comment('日付');
            $table->unsignedSmallInteger('number_of_holes')->comment('穴数');
            $table->unsignedTinyInteger('bed_row')->nullable()->comment('ベッド段');
            $table->unsignedTinyInteger('bed_column')->nullable()->comment('ベッド列');
            $table->unsignedTinyInteger('x_coordinate_panel')->nullable()->comment('X軸パネル数');
            $table->unsignedTinyInteger('y_coordinate_panel')->nullable()->comment('Y軸パネル数');
            $table->unsignedTinyInteger('x_current_bed_position')->nullable()->comment('X軸現在ベッド位置');
            $table->unsignedTinyInteger('y_current_bed_position')->nullable()->comment('Y軸現在ベッド位置');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->date('stage_start_date')->comment('ステージ開始日');
            $table->unsignedTinyInteger('growing_stage_sequence_number')->comment('生育ステージ連番');
            $table->string('cycle_pattern', 1)->comment('サイクルパターン');
            $table->unsignedTinyInteger('current_growth_stage')->comment('現在生育ステージ');
            $table->unsignedTinyInteger('next_growing_stage_sequence_number')->comment('次生育ステージ連番');
            $table->unsignedTinyInteger('next_growth_stage')->comment('次生育ステージ');
            $table->date('next_growth_stage_date')->comment('次生育ステージ移植日');
            $table->unsignedSmallInteger('using_hole_count')->nullable()->comment('使用穴数');
            $table->unsignedTinyInteger('panel_status')->nullable()->comment('パネル状況');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'panel_id', 'date'], 'panel_state_primary_key');
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
        Schema::dropIfExists('panel_state');
    }
}
