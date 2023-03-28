<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callendars', function (Blueprint $table) {
            $table->date('date')->comment('日付');
            $table->boolean('factory_is_rest')->default(false)->comment('工場休');
            $table->boolean('shipment_is_rest')->default(false)->comment('出荷休');
            $table->boolean('delivery_is_rest')->default(false)->comment('納入休');
            $table->string('remark', 50)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('callendars');
    }
}
