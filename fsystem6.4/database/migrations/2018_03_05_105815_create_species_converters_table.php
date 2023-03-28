<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpeciesConvertersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('species_converters', function (Blueprint $table) {
            $table->string('species_code', 15)->comment('品種コード');
            $table->string('product_large_category', 3)->default('')->comment('商品大カテゴリ');
            $table->string('product_middle_category', 3)->default('')->comment('商品中カテゴリ');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['species_code', 'product_large_category', 'product_middle_category'], 'species_converters_primary_keys');
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
        Schema::dropIfExists('species_converters');
    }
}
