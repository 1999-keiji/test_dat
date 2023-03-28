<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlannedArrangementDetailStatusWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planned_arrangement_detail_status_work', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('simulation_id')->comment('シミュレーションID');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->unsignedTinyInteger('display_kubun')->comment('表示区分');
            $table->date('date')->comment('日付');
            $table->unsignedTinyInteger('bed_row')->comment('ベッド段');
            $table->unsignedTinyInteger('bed_column')->comment('ベッド列');

            for($i=1;$i<=50;$i++) {
                $table->string('panel_status_'.$i, 2)->nullable()->comment('パネル状況'.$i);
            }

            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'simulation_id', 'factory_species_code', 'display_kubun', 'date', 'bed_row', 'bed_column'], 'planned_arrangement_detail_status_work_primary_key');
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
        Schema::dropIfExists('planned_arrangement_detail_status_work');
    }
}
