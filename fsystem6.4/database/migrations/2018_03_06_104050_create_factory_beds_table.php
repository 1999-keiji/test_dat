<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoryBedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_beds', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedTinyInteger('row')->comment('段');
            $table->unsignedTinyInteger('column')->comment('列');
            $table->unsignedTinyInteger('floor')->default(0)->comment('階');
            $table->unsignedTinyInteger('x_coordinate_panel')->default(0)->comment('X軸パネル数');
            $table->unsignedTinyInteger('y_coordinate_panel')->default(0)->comment('Y軸パネル数');
            $table->string('irradiation', 5)->default('')->comment('照明照射');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'row', 'column']);
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
        Schema::dropIfExists('factory_beds');
    }
}
