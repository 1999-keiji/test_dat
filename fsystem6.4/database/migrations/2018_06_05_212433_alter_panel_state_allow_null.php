<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPanelStateAllowNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE panel_state MODIFY growing_stage_sequence_number TINYINT UNSIGNED COMMENT '生育ステージ連番'");
        DB::statement("ALTER TABLE panel_state MODIFY current_growth_stage TINYINT UNSIGNED COMMENT '現在生育ステージ'");
        DB::statement("ALTER TABLE panel_state MODIFY next_growing_stage_sequence_number TINYINT UNSIGNED COMMENT '次生育ステージ連番'");
        DB::statement("ALTER TABLE panel_state MODIFY next_growth_stage TINYINT UNSIGNED COMMENT '次生育ステージ'");

        Schema::table('panel_state', function (Blueprint $table) {
            $table->string('factory_species_code', 15)->nullable()->comment('工場品種コード')->change();
            $table->date('stage_start_date')->nullable()->comment('ステージ開始日')->change();
            $table->string('cycle_pattern', 1)->nullable()->comment('サイクルパターン')->change();
            $table->date('next_growth_stage_date')->nullable()->comment('次生育ステージ移植日')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE panel_state MODIFY growing_stage_sequence_number TINYINT UNSIGNED NOT NULL COMMENT '生育ステージ連番'");
        DB::statement("ALTER TABLE panel_state MODIFY current_growth_stage TINYINT UNSIGNED NOT NULL COMMENT '現在生育ステージ'");
        DB::statement("ALTER TABLE panel_state MODIFY next_growing_stage_sequence_number TINYINT UNSIGNED NOT NULL COMMENT '次生育ステージ連番'");
        DB::statement("ALTER TABLE panel_state MODIFY next_growth_stage TINYINT UNSIGNED NOT NULL COMMENT '次生育ステージ'");

        Schema::table('panel_state', function (Blueprint $table) {
            $table->string('factory_species_code', 15)->comment('工場品種コード')->change();
            $table->date('stage_start_date')->comment('ステージ開始日')->change();
            $table->string('cycle_pattern', 1)->comment('サイクルパターン')->change();
            $table->date('next_growth_stage_date')->comment('次生育ステージ移植日')->change();
        });
    }
}
