<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlannedArrangementStatusWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planned_arrangement_status_work', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('simulation_id')->comment('シミュレーションID');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->unsignedTinyInteger('display_kubun')->comment('表示区分');
            $table->date('date')->comment('日付');
            $table->unsignedTinyInteger('bed_column')->comment('ベッド列');
            $table->unsignedTinyInteger('bed_row_number')->comment('ベッド段数');

            for($i=1;$i<=30;$i++) {
                $table->unsignedTinyInteger('growing_stages_count_'.$i)->nullable()->comment('生育ステージ段'.$i);
            }

            for($i=1;$i<=30;$i++) {
                $table->string('pattern_row_count_'.$i, 2)->nullable()->comment('パターン段'.$i);
            }

            $table->datetime('fixed_at')->nullable()->comment('確定日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'simulation_id', 'factory_species_code', 'display_kubun', 'date', 'bed_column'], 'planned_arrangement_status_work_primary_key');
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
        Schema::dropIfExists('planned_arrangement_status_work');
    }
}
