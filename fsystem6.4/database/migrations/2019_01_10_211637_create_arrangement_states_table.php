<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArrangementStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arrangement_states', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->date('start_of_week')->comment('作業週初日');
            $table->date('working_date')->comment('作業日');
            $table->unsignedTinyInteger('bed_column')->comment('ベッド列');
            $table->unsignedTinyInteger('bed_row_number')->comment('ベッド段数');

            for ($i = 1; $i <= 30; $i++) {
                $table->unsignedTinyInteger('growing_stages_count_'.$i)->nullable()->comment('生育ステージ段'.$i);
            }
            for ($i = 1; $i <= 30; $i++) {
                $table->string('pattern_row_count_'.$i, 2)->nullable()->comment('パターン段'.$i);
            }

            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary([
                'factory_code',
                'factory_species_code',
                'start_of_week',
                'working_date',
                'bed_column'
            ], 'arrangement_states_primary_key');

            $table->foreign([
                'factory_code',
                'factory_species_code',
                'start_of_week'
            ], 'bed_states_foreign_arrangement_states')
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
        Schema::dropIfExists('arrangement_states');
    }
}
