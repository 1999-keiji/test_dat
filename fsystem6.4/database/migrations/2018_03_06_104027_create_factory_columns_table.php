<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_columns', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedTinyInteger('column')->comment('列');
            $table->string('column_name', 5)->comment('列名');
            $table->unsignedTinyInteger('circulation')->default(0)->comment('循環');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'column']);
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
        Schema::dropIfExists('factory_columns');
    }
}
