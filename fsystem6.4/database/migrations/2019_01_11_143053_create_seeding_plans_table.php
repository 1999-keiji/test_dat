<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeedingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeding_plans', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->date('start_of_week')->comment('作業週初日');
            $table->date('working_date')->comment('作業日');
            $table->unsignedSmallInteger('number_of_trays')->default(0)->comment('トレイ数');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary([
                'factory_code',
                'factory_species_code',
                'start_of_week',
                'working_date'
            ], 'seeding_plans_primary_key');

            $table->foreign([
                'factory_code',
                'factory_species_code',
                'start_of_week'
            ], 'bed_states_foreign_seeding_plans')
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
        Schema::dropIfExists('seeding_plans');
    }
}
