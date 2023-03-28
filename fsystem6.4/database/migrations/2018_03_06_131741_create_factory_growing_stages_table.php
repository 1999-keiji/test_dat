<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryGrowingStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_growing_stages', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->unsignedTinyInteger('sequence_number')->comment('連番');
            $table->unsignedTinyInteger('growing_stage')->comment('生育ステージ');
            $table->string('growing_stage_name', 5)->default('')->comment('生育ステージ名');
            $table->string('label_color', 6)->default('')->comment('ラベルカラー');
            $table->unsignedTinyInteger('growing_term')->default(0)->comment('生育期間');
            $table->unsignedSmallInteger('number_of_holes')->comment('穴数');
            $table->unsignedDecimal('yield_rate', 3, 2)->default(0.00)->comment('歩留率');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'factory_species_code', 'sequence_number'], 'factory_growing_stages_primary');
            $table->foreign(['factory_code', 'factory_species_code'], 'factory_species_foreign')->references(['factory_code', 'factory_species_code'])->on('factory_species');
            $table->foreign(['factory_code', 'number_of_holes'], 'factory_panels_foreign')->references(['factory_code', 'number_of_holes'])->on('factory_panels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_growing_stages');
    }
}
