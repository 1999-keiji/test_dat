<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\AbroadShipmentPriceShowClass;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\StatementOfDeliveryClass;
use App\ValueObjects\Enum\StatementOfDeliveryPriceShowClass;
use App\ValueObjects\Enum\StatementOfDeliveryBuyerRemarkClass;
use App\ValueObjects\Enum\StatementOfDeliveryRemarkClass;

class CreateEndUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('end_users', function (Blueprint $table) {
            $table->string('end_user_code', 8)->comment('エンドユーザコード');
            $table->date('application_started_on')->comment('適用開始日');
            $table->unsignedTinyInteger('creating_type')->default(CreatingType::MANUAL_CREATED)->comment('登録種別');
            $table->string('customer_code', 15)->comment('得意先コード');
            $table->string('end_user_name', 50)->default('')->comment('エンドユーザ名称1');
            $table->string('end_user_name2', 50)->default('')->comment('エンドユーザ名称2');
            $table->string('end_user_abbreviation', 20)->default('')->comment('エンドユーザ略称');
            $table->string('end_user_name_kana', 30)->default('')->comment('エンドユーザカナ名称');
            $table->string('end_user_name_english', 65)->default('')->comment('エンドユーザ英字名称');
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
            $table->string('end_user_staff_name', 30)->default('')->comment('エンドユーザ担当者名');
            $table->string('currency_code', 3)->comment('エンドユーザ通貨コード');
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('seller_code', 8)->default('')->comment('販売担当コード');
            $table->string('seller_name', 30)->default('')->comment('販売担当者名');
            $table->string('pickup_slip_message', 40)->default('')->comment('出荷伝票コメント');
            $table->string('statement_of_delivery_class', 1)->default(StatementOfDeliveryClass::BASE_PLUS_NORMAL)->comment('納品書区分');
            $table->string('statement_of_delivery_price_show_class', 1)->default(StatementOfDeliveryPriceShowClass::ALL)->comment('納品書価格表示区分');
            $table->string('abroad_shipment_price_show_class', 1)->default(AbroadShipmentPriceShowClass::NOT_PRINT)->comment('海外出荷指示リスト価格表示区分');
            $table->string('export_managing_class', 1)->default('')->comment('輸出管理区分');
            $table->string('export_exchange_rate_code', 2)->default('')->comment('輸出管理建値コード');
            $table->string('remarks1', 50)->default('')->comment('REMARKS1');
            $table->string('remarks2', 50)->default('')->comment('REMARKS2');
            $table->string('remarks3', 50)->default('')->comment('REMARKS3');
            $table->string('remarks4', 50)->default('')->comment('REMARKS4');
            $table->string('remarks5', 50)->default('')->comment('REMARKS5');
            $table->string('remarks6', 50)->default('')->comment('REMARKS6');
            $table->string('loading_port_code', 4)->default('')->comment('積地コード');
            $table->string('loading_port_name', 30)->default('')->comment('積地名');
            $table->string('drop_port_code', 4)->default('')->comment('降地コード');
            $table->string('drop_port_name', 30)->default('')->comment('降地名');
            $table->string('exchange_rate_port_code', 4)->default('')->comment('建値地コード');
            $table->string('exchange_rate_port_name', 30)->default('')->comment('建値地名');
            $table->boolean('lot_managing_target_flag')->default(false)->comment('ロット管理対象フラグ');
            $table->string('end_user_remark', 50)->default('')->comment('エンドユーザ備考');
            $table->string('end_user_request_number', 5)->default('')->comment('エンドユーザ要求番号');
            $table->string('statement_of_delivery_remark_class', 1)->default(StatementOfDeliveryRemarkClass::CLIENT_REMARK)->comment('納品書備考印字区分');
            $table->string('statement_of_delivery_buyer_remark_class', 1)->default(StatementOfDeliveryBuyerRemarkClass::INDIVIDUAL_USE)->comment('納品書発注者使用欄印字区分');
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

            $table->primary(['end_user_code', 'application_started_on']);
            $table->foreign('customer_code')->references('customer_code')->on('customers');
            $table->foreign('currency_code')->references('currency_code')->on('currencies');
            $table->foreign('delivery_destination_code')->references('delivery_destination_code')->on('delivery_destinations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('end_users');
    }
}
