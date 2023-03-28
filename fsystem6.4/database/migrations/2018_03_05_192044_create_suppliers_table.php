<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\CreatingType;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->string('supplier_code', 8)->comment('仕入先コード');
            $table->date('application_started_on')->comment('適用開始日');
            $table->unsignedTinyInteger('creating_type')->default(CreatingType::MANUAL_CREATED)->comment('登録種別');
            $table->string('supplier_name', 50)->default('')->comment('仕入先名称1');
            $table->string('supplier_name2', 50)->default('')->comment('仕入先名称2');
            $table->string('supplier_abbreviation', 20)->default('')->comment('仕入先略称');
            $table->string('supplier_name_kana', 30)->default('')->comment('仕入先カナ名称');
            $table->string('supplier_name_english', 65)->default('')->comment('仕入先英字名称');
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
            $table->string('mail_address', 30)->default('')->comment('メールアドレス');
            $table->string('supplier_staff_name', 30)->default('')->comment('仕入先担当者名');
            $table->string('currency_code', 3)->comment('仕入先通貨コード');
            $table->string('supplier_class', 1)->comment('仕入先区分');
            $table->boolean('export_target_flag')->default(false)->comment('輸出対象フラグ');
            $table->boolean('group_company_flag')->default(false)->comment('グループ会社フラグ');
            $table->string('company_code', 6)->default('')->comment('企業コード');
            $table->string('company_name', 50)->default('')->comment('企業名称');
            $table->string('company_abbreviation', 20)->default('')->comment('企業略称');
            $table->string('company_name_kana', 30)->default('')->comment('企業カナ名称');
            $table->string('company_name_english', 50)->default('')->comment('企業英字名称');
            $table->string('company_group_code', 6)->default('')->comment('企業グループコード');
            $table->string('company_group_name', 40)->default('')->comment('企業グループ名称');
            $table->string('company_group_name_english', 50)->default('')->comment('企業グループ英字名称');
            $table->boolean('can_display')->default(true)->comment('表示区分');
            $table->string('remark', 255)->default('')->comment('備考');
            $table->string('reserved_text1', 200)->default('')->comment('予備文字項目1');
            $table->string('reserved_text2', 200)->default('')->comment('予備文字項目2');
            $table->string('reserved_text3', 200)->default('')->comment('予備文字項目3');
            $table->string('reserved_text4', 200)->default('')->comment('予備文字項目4');
            $table->string('reserved_text5', 200)->default('')->comment('予備文字項目5');
            $table->string('reserved_text6', 200)->default('')->comment('予備文字項目6');
            $table->string('reserved_text7', 200)->default('')->comment('予備文字項目7');
            $table->string('reserved_text8', 200)->default('')->comment('予備文字項目8');
            $table->string('reserved_text9', 200)->default('')->comment('予備文字項目9');
            $table->string('reserved_text10', 200)->default('')->comment('予備文字項目10');
            $table->decimal('reserved_number1', 18, 5)->default(null)->nullable()->comment('予備数値項目1');
            $table->decimal('reserved_number2', 18, 5)->default(null)->nullable()->comment('予備数値項目2');
            $table->decimal('reserved_number3', 18, 5)->default(null)->nullable()->comment('予備数値項目3');
            $table->decimal('reserved_number4', 18, 5)->default(null)->nullable()->comment('予備数値項目4');
            $table->decimal('reserved_number5', 18, 5)->default(null)->nullable()->comment('予備数値項目5');
            $table->decimal('reserved_number6', 18, 5)->default(null)->nullable()->comment('予備数値項目6');
            $table->decimal('reserved_number7', 18, 5)->default(null)->nullable()->comment('予備数値項目7');
            $table->decimal('reserved_number8', 18, 5)->default(null)->nullable()->comment('予備数値項目8');
            $table->decimal('reserved_number9', 18, 5)->default(null)->nullable()->comment('予備数値項目9');
            $table->decimal('reserved_number10', 18, 5)->default(null)->nullable()->comment('予備数値項目10');
            $table->boolean('base_plus_delete_flag')->default(null)->nullable()->comment('BASE+削除フラグ');
            $table->string('base_plus_user_created_by', 8)->default(null)->nullable()->comment('BASE+作成者');
            $table->string('base_plus_program_created_by', 12)->default(null)->nullable()->comment('BASE+作成プログラム');
            $table->datetime('base_plus_created_at')->default(null)->nullable()->comment('BASE+作成日時');
            $table->string('base_plus_user_updated_by', 8)->default(null)->nullable()->comment('BASE+更新者');
            $table->string('base_plus_program_updated_by', 12)->default(null)->nullable()->comment('BASE+更新プログラム');
            $table->datetime('base_plus_updated_at')->default(null)->nullable()->comment('BASE+更新日時');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['supplier_code', 'application_started_on']);
            $table->foreign('currency_code')->references('currency_code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
