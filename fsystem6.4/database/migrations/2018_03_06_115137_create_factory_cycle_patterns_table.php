<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryCyclePatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_cycle_patterns', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('pattern', 1)->comment('パターン');
            $table->unsignedTinyInteger('day_of_the_week')->comment('曜日');
            $table->unsignedTinyInteger('number_of_beds')->comment('移動ベッド数');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'pattern', 'day_of_the_week'], 'factory_cycle_patterns_primary');
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
        Schema::dropIfExists('factory_cycle_patterns');
    }
}
