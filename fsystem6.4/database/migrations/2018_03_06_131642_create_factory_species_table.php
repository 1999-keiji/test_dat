<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactorySpeciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_species', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_species_code', 15)->comment('工場品種コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->string('factory_species_name', 30)->default('')->comment('工場品種名');
            $table->unsignedMediumInteger('weight')->default(0)->comment('重量');
            $table->string('remark', 50)->string('')->comment('備考');
            $table->boolean('can_select_on_simulation')->default(true)->comment('生産シミュレーション時選択区分');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'factory_species_code'], 'factory_species_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('species_code')->references('species_code')->on('species');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_species');
    }
}
