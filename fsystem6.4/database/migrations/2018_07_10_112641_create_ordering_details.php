<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordering_details', function (Blueprint $table) {
            $table->string('own_company_code', 6)->comment('会社コード');
            $table->string('place_order_number', 10)->comment('発注番号');
            $table->string('place_order_chapter_number', 3)->comment('発注項番');
            $table->string('place_order_status_code', 3)->comment('発注ステータスコード');
            $table->string('place_order_annulment_reason_code', 2)->comment('発注取引理由コード');
            $table->boolean('edl_send_compleate_flag')->default(null)->nullable()->comment('ＥＤＩ送信済フラグ');
            $table->string('edl_information_class', 4)->comment('ＥＤＩ情報区分');
            $table->string('place_order_date', 8)->comment('発注年月日');
            $table->string('order_contract_receive_date', 8)->comment('注文請け受信年月日');
            $table->string('order_class', 1)->comment('注文受諾区分');
            $table->string('product_class', 1)->comment('製品区分');
            $table->string('supplier_product_name', 50)->comment('仕入先品名');
            $table->string('customer_product_name', 50)->comment('得意先品名');
            $table->string('product_name', 40)->comment('品名');
            $table->string('place_order_rank1_code', 3)->comment('発注規格1');
            $table->string('place_order_rank2_code', 3)->comment('発注規格2');
            $table->string('place_order_rank3_code', 3)->comment('発注規格3');
            $table->string('saller_rank_code', 12)->comment('営業規格');
            $table->string('special_spec_code', 10)->comment('特殊仕様');
            $table->string('product_number', 15)->comment('品番');
            $table->string('maker_code', 8)->comment('メーカーコード');
            $table->string('request_delivery_date', 8)->comment('希望納期');
            $table->string('answer_delivery_date', 8)->comment('回答納期');
            $table->string('requestor_code', 10)->comment('要求元コード');
            $table->string('requestor_organization_code', 6)->comment('要求元組織コード');
            $table->string('organization_name', 40)->comment('組織名');
            $table->string('customer_code', 8)->comment('得意先コード');
            $table->string('customer_place_order_last_code', 8)->comment('発注得意先コード');
            $table->string('base_plue_end_user_code', 10)->comment('最終顧客コード');
            $table->unsignedInteger('supplier_place_order_quantity')->comment('仕入先発注数');
            $table->unsignedInteger('supplier_purchase_compleate_quantity')->comment('仕入先仕入済数');
            $table->unsignedInteger('supplier_closed_quantity')->comment('仕入先打切数');
            $table->unsignedInteger('place_order_quantity')->comment('発注数');
            $table->unsignedInteger('arrival_plan_quantity')->comment('仕入先入荷予定数');
            $table->unsignedInteger('arrival_compleate_quantity')->comment('入荷済数');
            $table->unsignedInteger('stock_compleate_quantity')->comment('入庫済数');
            $table->unsignedInteger('purchase_compleate_quantity')->comment('仕入済数');
            $table->unsignedInteger('cancellation_quantity')->comment('解約数');
            $table->unsignedInteger('cancellation_delivery_compleate_quantity')->comment('解約受入済数');
            $table->string('place_order_unit_code', 3)->comment('発注単位');
            $table->string('price_number', 15)->comment('価格番号');
            $table->unsignedDecimal('supplier_place_order_unit', 14, 5)->default(0.00000)->comment('仕入先発注単価');
            $table->unsignedDecimal('place_order_amount', 16, 3)->default(0.00000)->comment('発注合価');
            $table->unsignedDecimal('place_order_unit', 14, 5)->default(0.00000)->comment('発注単価');
            $table->boolean('minimum_place_order_unit_flag')->default(null)->nullable()->comment('最低発注単位チェックフラグ');
            $table->boolean('place_order_flag')->default(null)->nullable()->comment('発注単位チェックフラグ');
            $table->string('delivery_method_class', 1)->comment('納入方法区分');
            $table->boolean('contract_flag')->default(null)->nullable()->comment('請負契約フラグ');
            $table->string('stock_rank_class', 1)->comment('在庫ランク区分');
            $table->string('place_order_message', 50)->comment('発注コメント');
            $table->string('supplier_instructions', 50)->comment('仕入先指示');
            $table->string('buyer_remark', 50)->comment('発注者備考');
            $table->string('delivery_destination_code', 10)->comment('納入先コード');
            $table->string('warehouse_code', 10)->comment('倉庫コード');
            $table->boolean('orders_sheet_issue_flag')->default(null)->nullable()->comment('注文書発行フラグ');
            $table->string('orders_sheet_issue_date', 8)->comment('注文書発行年月日');
            $table->string('arrangement_class', 1)->comment('手配区分');
            $table->string('construction_develop_number', 10)->comment('工事開発番号');
            $table->string('el_approval_number', 18)->comment('ＥＬ承認番号');
            $table->string('estimation_approval_number', 15)->comment('見積承認番号');
            $table->string('supplier_recived_order_number', 14)->comment('仕入先受注番号');
            $table->string('supplier_original_recived_order_pickup_number', 14)->comment('仕入先元受注伝票番号');
            $table->string('manufacturing_class', 2)->comment('加工区分');
            $table->string('inspection_spec_sentence', 20)->comment('検査仕様');
            $table->unsignedSmallInteger('inspection_circulation')->nullable()->comment('検査部数');
            $table->string('statement_delivery_date', 8)->comment('納品書年月日');
            $table->string('set_product_name', 40)->comment('セット品名');
            $table->string('set_maker', 8)->comment('セット品メーカー');
            $table->string('set_rank1_code', 3)->comment('セット品規格１');
            $table->string('set_rank2_code', 3)->comment('セット品規格２');
            $table->string('set_rank3_code', 3)->comment('セット品規格３');
            $table->string('set_special_spec_code', 10)->comment('セット品特殊仕様');
            $table->boolean('repair_order_flag')->default(null)->nullable()->comment('修理オーダフラグ');
            $table->string('goods_name', 40)->comment('現品名称');
            $table->unsignedInteger('goods_quantity')->comment('現品数量');
            $table->string('warehouse_supplier_to_code', 10)->comment('支給先倉庫コード');
            $table->string('original_place_order_number', 10)->comment('元発注番号');
            $table->string('original_place_order_chapter_number', 3)->comment('元発注項番');
            $table->string('original_place_order_delivery_lead_number', 2)->comment('元発注納番');
            $table->string('original_place_order_branch_number', 2)->comment('元発注枝番');
            $table->string('approval_number', 12)->comment('承認管理番号');
            $table->boolean('direct_shipment_flag')->default(null)->nullable()->comment('直送フラグ');
            $table->boolean('compleate_flag')->default(null)->nullable()->comment('完了フラグ');
            $table->boolean('unofficial_recived_order_flag')->default(null)->nullable()->comment('内示受注フラグ');
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

            $table->primary(['own_company_code', 'place_order_number', 'place_order_chapter_number'], 'ordering_details_primary_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordering_details');
    }
}
