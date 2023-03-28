<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBedStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bed_states', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->date('start_of_week')->comment('作業週初日');
            $table->datetime('started_preparation_at')->comment('準備開始日時');
            $table->datetime('completed_preparation_at')->nullable()->comment('準備完了日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'factory_species_code', 'start_of_week'], 'bed_states_primary_key');
            $table->foreign(['factory_code', 'factory_species_code'], 'factory_species_foreign_bed_states')
                ->references(['factory_code', 'factory_species_code'])
                ->on('factory_species');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bed_states');
    }
}
