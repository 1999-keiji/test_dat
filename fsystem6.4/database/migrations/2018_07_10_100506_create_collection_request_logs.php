<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionRequestLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_request_logs', function (Blueprint $table) {
            $table->string('order_number', 14)->comment('Fsystem注文番号');
            $table->unsignedInteger('sequence_number')->comment('連番');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['order_number', 'sequence_number'], 'collection_request_logs_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_request_logs');
    }
}
