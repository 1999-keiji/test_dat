<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\DeliveryDestinationClass;

class CreateDeliveryDestinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_destinations', function (Blueprint $table) {
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->unsignedTinyInteger('creating_type')->default(CreatingType::MANUAL_CREATED)->comment('登録種別');
            $table->string('delivery_destination_name', 50)->default('')->comment('納入先名称1');
            $table->string('delivery_destination_name2', 50)->default('')->comment('納入先名称2');
            $table->string('delivery_destination_abbreviation', 20)->default('')->comment('納入先略称');
            $table->string('delivery_destination_name_kana', 30)->default('')->comment('納入先カナ名称');
            $table->string('delivery_destination_name_english', 65)->default('')->comment('納入先英字名称');
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
            $table->string('staff_abbreviation', 10)->default('')->comment('納入先担当者略称');
            $table->string('statement_of_delivery_message', 40)->default('')->comment('納品書コメント');
            $table->string('statement_of_delivery_output_class', 1)->default('')->comment('納品書出力区分');
            $table->boolean('shipping_label_unnecessary_flag')->default(false)->comment('送り状不要フラグ');
            $table->boolean('export_target_flag')->default(false)->comment('輸出対象フラグ');
            $table->string('shipment_way_class', 2)->default('')->comment('納入先配送方法区分');
            $table->string('delivery_destination_class', 1)->default(DeliveryDestinationClass::DOMESTIC)->comment('納入先区分');
            $table->string('cii_company_code', 12)->default('')->comment('CII統一企業コード');
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

            $table->primary('delivery_destination_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_destinations');
    }
}
