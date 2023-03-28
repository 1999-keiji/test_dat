<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\CreatingType;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_number', 14)->comment('Fsystem注文番号');
            $table->string('base_plus_order_number', 10)->nullable()->comment('Base+注文番号');
            $table->string('base_plus_order_chapter_number', 3)->nullable()->comment('Base+注文番号');
            $table->datetime('received_date')->nullable()->comment('注文日');
            $table->string('prodcut_class', 1)->default(1)->comment('製品区分');
            $table->string('supplier_product_name', 50)->default('')->comment('仕入先品名');
            $table->string('customer_product_name', 50)->default('')->comment('得意先品名');
            $table->string('product_name', 40)->default('')->comment('品名');
            $table->string('special_spec_code', 10)->default('')->comment('特殊仕様');
            $table->string('product_code', 15)->default('')->comment('商品コード');
            $table->string('maker_code', 8)->default('')->comment('メーカーコード');
            $table->datetime('delivery_date')->nullable()->comment('納期');
            $table->string('requestor_organization_code', 6)->default('')->comment('要求元組織コード');
            $table->string('organization_name', 4)->default('')->comment('組織名');
            $table->string('end_user_code', 8)->default('')->comment('エンドユーザコード');
            $table->string('base_plus_end_user_code', 10)->default('')->comment('最終顧客コード');
            $table->unsignedInteger('order_quantity')->nullable()->comment('注文数');
            $table->unsignedInteger('place_order_quantity')->default(0)->comment('発注数');
            $table->string('place_order_unit_code', 3)->default('')->comment('発注単位');
            $table->unsignedDecimal('supplier_place_order_unit', 14, 5)->default(0.00000)->comment('仕入先発注単価');
            $table->unsignedDecimal('order_amount', 16, 3)->nullable()->comment('合価');
            $table->unsignedDecimal('order_unit', 14, 5)->nullable()->comment('単価');
            $table->string('order_message', 50)->default('')->comment('備考');
            $table->string('supplier_instructions', 50)->default('')->comment('仕入先指示');
            $table->string('buyer_remark', 50)->default('')->comment('発注者備考');
            $table->string('delivery_destination_code', 10)->default('')->comment('納入先コード');
            $table->unsignedDecimal('recived_order_unit', 14, 5)->comment('受注単価');
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->comment('得意先受注合価');
            $table->string('process_class', 1)->default(1)->comment('処理区分');
            $table->string('own_company_code', 6)->default('')->comment('会社コード');
            $table->string('small_peace_of_peper_type_class', 1)->default('C')->comment('発注伝票種別区分');
            $table->string('small_peace_of_peper_type_code', 2)->default('01')->comment('発注伝票種別コード');
            $table->string('supplier_flag', 8)->default('')->comment('仕入先コード');
            $table->string('tax_class', 1)->default(1)->comment('課税区分');
            $table->string('purchase_staff_code', 8)->default('')->comment('購買担当者コード');
            $table->string('purchase_staff_name', 30)->default('')->comment('購買担当者名');
            $table->string('currency_code', 3)->default('')->comment('通貨コード');
            $table->string('place_order_work_staff_code', 8)->default('')->comment('発注業務担当者コード');
            $table->string('place_order_work_staff_name', 30)->default('')->comment('発注業務担当者名');
            $table->string('end_user_order_number', 17)->default('')->nullable()->comment('エンドユーザ注文番号');
            $table->string('pickup_type_class', 1)->default('A')->comment('受注伝票種別区分');
            $table->string('pickup_type_code', 2)->default('01')->comment('受注伝票種別コード');
            $table->string('basis_for_recording_sales_class', 1)->default(1)->comment('売上計上基準区分');
            $table->string('customer_code', 8)->default('')->comment('得意先コード');
            $table->string('statement_delivery_price_display_class', 1)->default(1)->comment('納品書価格表示区分');
            $table->string('seller_code', 8)->default('')->comment('販売担当者コード');
            $table->string('seller_name', 30)->default('')->comment('販売担当者名');
            $table->string('customer_staff_name', 30)->default('')->comment('得意先担当者名');
            $table->string('factory_code', 15)->nullable()->comment('工場コード');
            $table->date('shipment_date')->comment('出荷日');
            $table->unsignedTinyInteger('creating_type')->default(CreatingType::MANUAL_CREATED)->comment('登録種別');
            $table->unsignedTinyInteger('slip_type')->nullable()->comment('伝票種別');
            $table->unsignedTinyInteger('slip_status_type')->comment('伝票状態種別');
            $table->unsignedTinyInteger('raleted_order_status_type')->default(0)->comment('紐付状態種別');
            $table->boolean('factory_cancel_flag')->default(false)->comment('工場キャンセルフラグ');
            $table->unsignedInteger('returned_product_quantity')->default(0)->comment('返品数');
            $table->string('returned_product_remark', 50)->default('')->comment('返品備考');
            $table->string('transpote_company_code', 15)->nullable()->comment('運送会社コード');
            $table->unsignedInteger('collection_time_nomber')->nullable()->comment('集荷時間連番');
            $table->string('fixed_shipping_by', 15)->default('')->comment('出荷確定者');
            $table->datetime('fixed_shipping_at')->nullable()->comment('出荷確定日時');
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

            $table->primary('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
