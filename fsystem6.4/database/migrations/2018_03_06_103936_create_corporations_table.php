<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporations', function (Blueprint $table) {
            $table->string('corporation_code', 6)->comment('法人コード');
            $table->string('corporation_name', 50)->default('')->comment('法人名');
            $table->string('corporation_abbreviation', 20)->default('')->comment('法人略称');
            $table->string('country_code', 2)->default('')->comment('国コード');
            $table->string('postal_code', 10)->default('')->comment('郵便番号');
            $table->string('prefecture_code', 3)->default('')->comment('都道府県コード');
            $table->string('address', 50)->default('')->comment('住所1');
            $table->string('address2', 50)->default('')->comment('住所2');
            $table->string('address3', 50)->default('')->comment('住所3');
            $table->string('abroad_address', 50)->default('')->comment('海外住所1');
            $table->string('abroad_address2', 50)->default('')->comment('海外住所2');
            $table->string('abroad_address3', 50)->default('')->comment('海外住所3');
            $table->string('phone_number', 20)->default('')->comment('電話番号');
            $table->string('extension_number', 15)->default('')->comment('内線番号');
            $table->string('fax_number', 15)->default('')->comment('FAX番号');
            $table->string('mail_address', 30)->default('')->comment('メールアドレス');
            $table->string('remark', 255)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary('corporation_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporations');
    }
}
