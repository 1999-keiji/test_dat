<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_time', function (Blueprint $table) {
            $table->string('transpote_company_code', 15)->comment('運送会社コード');
            $table->unsignedInteger('sequence_number')->comment('連番');
            $table->string('collection_time', 20)->default('')->comment('集荷時間');
            $table->string('remark', 100)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['transpote_company_code', 'sequence_number'], 'collection_time_primary_key');
            $table->foreign('transpote_company_code')->references('transpote_company_code')->on('transpote_companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_time');
    }
}
