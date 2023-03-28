<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('factory_name', 50)->default('')->comment('工場名');
            $table->string('factory_abbreviation', 20)->default('')->comment('工場略称');
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
            $table->boolean('work_on_saturday')->default(false)->comment('土曜営業');
            $table->boolean('work_on_sunday')->default(false)->comment('日曜営業');
            $table->string('corporation_code', 6)->comment('法人コード');
            $table->string('supplier_code', 8)->default('')->comment('仕入先コード');
            $table->string('remark', 255)->default('')->comment('備考');
            $table->string('invoice_corporation_name', 50)->default('')->comment('請求書会社名');
            $table->string('invoice_postal_code', 10)->default('')->comment('請求書郵便番号');
            $table->string('invoice_address', 50)->default('')->comment('請求書住所');
            $table->string('invoice_phone_number', 20)->default('')->comment('請求書電話番号');
            $table->string('invoice_fax_number', 15)->default('')->comment('請求書FAX番号');
            $table->unsignedTinyInteger('number_of_floors')->default(0)->comment('階数');
            $table->unsignedTinyInteger('number_of_rows')->default(0)->comment('段数');
            $table->unsignedTinyInteger('number_of_columns')->default(0)->comment('列数');
            $table->unsignedTinyInteger('number_of_circulation')->default(0)->comment('循環数');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary('factory_code');
            $table->foreign('corporation_code')->references('corporation_code')->on('corporations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factories');
    }
}
