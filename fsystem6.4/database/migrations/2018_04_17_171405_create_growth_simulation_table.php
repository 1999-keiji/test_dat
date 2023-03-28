<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrowthSimulationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('growth_simulation', function (Blueprint $table){
            $table->string('factory_code', 15)->comment('工場コード');
            $table->integer('simulation_id')->comment('シミュレーションID');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->string('simulation_name', 50)->comment('シミュレーション名');
            $table->tinyInteger('detail_number')->comment('明細数');
            $table->string('fixed_by', 15)->nullable()->comment('確定者');
            $table->datetime('fixed_start_at')->nullable()->comment('確定開始日時');
            $table->datetime('fixed_comp_at')->nullable()->comment('確定完了日時');
            $table->string('work_by', 15)->nullable()->comment('作業者');
            $table->datetime('work_at')->nullable()->comment('作業開始日時');
            $table->datetime('simulation_preparation_start_at')->nullable()->comment('シミュレーション準備開始日時');
            $table->datetime('simulation_preparation_comp_at')->nullable()->comment('シミュレーション準備完了日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'simulation_id', 'factory_species_code'], 'growth_simulation_primary_key');
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
        Schema::dropIfExists('growth_simulation');
    }
}
