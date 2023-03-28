<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowthSimulationItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('growth_simulation_item', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('simulation_id')->comment('シミュレーションID');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->tinyInteger('detail_id')->comment('明細ID');
            $table->unsignedTinyInteger('growing_stages_sequence_number')->comment('生育ステージ連番');
            $table->unsignedTinyInteger('input_change')->comment('入力切替区分');
            $table->unsignedTinyInteger('growing_stage')->comment('生育ステージ');
            $table->date('date')->nullable()->comment('日付');
            $table->unsignedSmallInteger('bed_number')->nullable()->comment('ベッド数');
            $table->unsignedSmallInteger('panel_number')->nullable()->comment('パネル数');
            $table->unsignedInteger('stock_number')->nullable()->comment('株数');
            $table->unsignedTinyInteger('growth_days')->nullable()->comment('生育日数');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'simulation_id', 'factory_species_code', 'detail_id', 'growing_stages_sequence_number'], 'growth_simulation_item_primary_key');
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
        Schema::dropIfExists('growth_simulation_item');
    }
}
