<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderingDetailsChangeNullRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordering_details', function (Blueprint $table) {
            $table->string('place_order_status_code', 3)->comment('発注ステータスコード')->nullable()->change();
            $table->string('place_order_annulment_reason_code', 2)->comment('発注取引理由コード')->nullable()->change();
            $table->string('edl_information_class', 4)->comment('ＥＤＩ情報区分')->nullable()->change();
            $table->string('place_order_date', 8)->comment('発注年月日')->nullable()->change();
            $table->string('order_contract_receive_date', 8)->comment('注文請け受信年月日')->nullable()->change();
            $table->string('order_class', 1)->comment('注文受諾区分')->nullable()->change();
            $table->string('product_class', 1)->comment('製品区分')->nullable()->change();
            $table->string('supplier_product_name', 50)->comment('仕入先品名')->nullable()->change();
            $table->string('customer_product_name', 50)->comment('得意先品名')->nullable()->change();
            $table->string('product_name', 40)->comment('品名')->nullable()->change();
            $table->string('place_order_rank1_code', 3)->comment('発注規格1')->nullable()->change();
            $table->string('place_order_rank2_code', 3)->comment('発注規格2')->nullable()->change();
            $table->string('place_order_rank3_code', 3)->comment('発注規格3')->nullable()->change();
            $table->string('saller_rank_code', 12)->comment('営業規格')->nullable()->change();
            $table->string('special_spec_code', 10)->comment('特殊仕様')->nullable()->change();
            $table->string('product_number', 15)->comment('品番')->nullable()->change();
            $table->string('maker_code', 8)->comment('メーカーコード')->nullable()->change();
            $table->string('request_delivery_date', 8)->comment('希望納期')->nullable()->change();
            $table->string('answer_delivery_date', 8)->comment('回答納期')->nullable()->change();
            $table->string('requestor_code', 10)->comment('要求元コード')->nullable()->change();
            $table->string('requestor_organization_code', 6)->comment('要求元組織コード')->nullable()->change();
            $table->string('organization_name', 40)->comment('組織名')->nullable()->change();
            $table->string('customer_code', 8)->comment('得意先コード')->nullable()->change();
            $table->string('customer_place_order_last_code', 8)->comment('発注得意先コード')->nullable()->change();
            $table->string('base_plue_end_user_code', 10)->comment('最終顧客コード')->nullable()->change();
            $table->unsignedInteger('supplier_place_order_quantity')->comment('仕入先発注数')->nullable()->change();
            $table->unsignedInteger('supplier_purchase_compleate_quantity')->comment('仕入先仕入済数')->nullable()->change();
            $table->unsignedInteger('supplier_closed_quantity')->comment('仕入先打切数')->nullable()->change();
            $table->unsignedInteger('place_order_quantity')->comment('発注数')->nullable()->change();
            $table->unsignedInteger('arrival_plan_quantity')->comment('仕入先入荷予定数')->nullable()->change();
            $table->unsignedInteger('arrival_compleate_quantity')->comment('入荷済数')->nullable()->change();
            $table->unsignedInteger('stock_compleate_quantity')->comment('入庫済数')->nullable()->change();
            $table->unsignedInteger('purchase_compleate_quantity')->comment('仕入済数')->nullable()->change();
            $table->unsignedInteger('cancellation_quantity')->comment('解約数')->nullable()->change();
            $table->unsignedInteger('cancellation_delivery_compleate_quantity')->comment('解約受入済数')->nullable()->change();
            $table->string('place_order_unit_code', 3)->comment('発注単位')->nullable()->change();
            $table->string('price_number', 15)->comment('価格番号')->nullable()->change();
            $table->unsignedDecimal('supplier_place_order_unit', 14, 5)->default(0.00000)->comment('仕入先発注単価')->nullable()->change();
            $table->unsignedDecimal('place_order_amount', 16, 3)->default(0.00000)->comment('発注合価')->nullable()->change();
            $table->unsignedDecimal('place_order_unit', 14, 5)->default(0.00000)->comment('発注単価')->nullable()->change();
            $table->string('delivery_method_class', 1)->comment('納入方法区分')->nullable()->change();
            $table->string('stock_rank_class', 1)->comment('在庫ランク区分')->nullable()->change();
            $table->string('place_order_message', 50)->comment('発注コメント')->nullable()->change();
            $table->string('supplier_instructions', 50)->comment('仕入先指示')->nullable()->change();
            $table->string('buyer_remark', 50)->comment('発注者備考')->nullable()->change();
            $table->string('delivery_destination_code', 10)->comment('納入先コード')->nullable()->change();
            $table->string('warehouse_code', 10)->comment('倉庫コード')->nullable()->change();
            $table->string('orders_sheet_issue_date', 8)->comment('注文書発行年月日')->nullable()->change();
            $table->string('arrangement_class', 1)->comment('手配区分')->nullable()->change();
            $table->string('construction_develop_number', 10)->comment('工事開発番号')->nullable()->change();
            $table->string('el_approval_number', 18)->comment('ＥＬ承認番号')->nullable()->change();
            $table->string('estimation_approval_number', 15)->comment('見積承認番号')->nullable()->change();
            $table->string('supplier_recived_order_number', 14)->comment('仕入先受注番号')->nullable()->change();
            $table->string('supplier_original_recived_order_pickup_number', 14)->comment('仕入先元受注伝票番号')->nullable()->change();
            $table->string('manufacturing_class', 2)->comment('加工区分')->nullable()->change();
            $table->string('inspection_spec_sentence', 20)->comment('検査仕様')->nullable()->change();
            $table->string('statement_delivery_date', 8)->comment('納品書年月日')->nullable()->change();
            $table->string('set_product_name', 40)->comment('セット品名')->nullable()->change();
            $table->string('set_maker', 8)->comment('セット品メーカー')->nullable()->change();
            $table->string('set_rank1_code', 3)->comment('セット品規格１')->nullable()->change();
            $table->string('set_rank2_code', 3)->comment('セット品規格２')->nullable()->change();
            $table->string('set_rank3_code', 3)->comment('セット品規格３')->nullable()->change();
            $table->string('set_special_spec_code', 10)->comment('セット品特殊仕様')->nullable()->change();
            $table->string('goods_name', 40)->comment('現品名称')->nullable()->change();
            $table->unsignedInteger('goods_quantity')->comment('現品数量')->nullable()->change();
            $table->string('warehouse_supplier_to_code', 10)->comment('支給先倉庫コード')->nullable()->change();
            $table->string('original_place_order_number', 10)->comment('元発注番号')->nullable()->change();
            $table->string('original_place_order_chapter_number', 3)->comment('元発注項番')->nullable()->change();
            $table->string('original_place_order_delivery_lead_number', 2)->comment('元発注納番')->nullable()->change();
            $table->string('original_place_order_branch_number', 2)->comment('元発注枝番')->nullable()->change();
            $table->string('approval_number', 12)->comment('承認管理番号')->nullable()->change();
            $table->string('reserved_text1', 200)->default('')->comment('予備文字項目1')->nullable()->change();
            $table->string('reserved_text2', 200)->default('')->comment('予備文字項目2')->nullable()->change();
            $table->string('reserved_text3', 200)->default('')->comment('予備文字項目3')->nullable()->change();
            $table->string('reserved_text4', 200)->default('')->comment('予備文字項目4')->nullable()->change();
            $table->string('reserved_text5', 200)->default('')->comment('予備文字項目5')->nullable()->change();
            $table->string('reserved_text6', 200)->default('')->comment('予備文字項目6')->nullable()->change();
            $table->string('reserved_text7', 200)->default('')->comment('予備文字項目7')->nullable()->change();
            $table->string('reserved_text8', 200)->default('')->comment('予備文字項目8')->nullable()->change();
            $table->string('reserved_text9', 200)->default('')->comment('予備文字項目9')->nullable()->change();
            $table->string('reserved_text10', 200)->default('')->comment('予備文字項目10')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordering_details', function (Blueprint $table) {
            $table->string('place_order_status_code', 3)->comment('発注ステータスコード')->nullable(false)->change();
            $table->string('place_order_annulment_reason_code', 2)->comment('発注取引理由コード')->nullable(false)->change();
            $table->string('edl_information_class', 4)->comment('ＥＤＩ情報区分')->nullable(false)->change();
            $table->string('place_order_date', 8)->comment('発注年月日')->nullable(false)->change();
            $table->string('order_contract_receive_date', 8)->comment('注文請け受信年月日')->nullable(false)->change();
            $table->string('order_class', 1)->comment('注文受諾区分')->nullable(false)->change();
            $table->string('product_class', 1)->comment('製品区分')->nullable(false)->change();
            $table->string('supplier_product_name', 50)->comment('仕入先品名')->nullable(false)->change();
            $table->string('customer_product_name', 50)->comment('得意先品名')->nullable(false)->change();
            $table->string('product_name', 40)->comment('品名')->nullable(false)->change();
            $table->string('place_order_rank1_code', 3)->comment('発注規格1')->nullable(false)->change();
            $table->string('place_order_rank2_code', 3)->comment('発注規格2')->nullable(false)->change();
            $table->string('place_order_rank3_code', 3)->comment('発注規格3')->nullable(false)->change();
            $table->string('saller_rank_code', 12)->comment('営業規格')->nullable(false)->change();
            $table->string('special_spec_code', 10)->comment('特殊仕様')->nullable(false)->change();
            $table->string('product_number', 15)->comment('品番')->nullable(false)->change();
            $table->string('maker_code', 8)->comment('メーカーコード')->nullable(false)->change();
            $table->string('request_delivery_date', 8)->comment('希望納期')->nullable(false)->change();
            $table->string('answer_delivery_date', 8)->comment('回答納期')->nullable(false)->change();
            $table->string('requestor_code', 10)->comment('要求元コード')->nullable(false)->change();
            $table->string('requestor_organization_code', 6)->comment('要求元組織コード')->nullable(false)->change();
            $table->string('organization_name', 40)->comment('組織名')->nullable(false)->change();
            $table->string('customer_code', 8)->comment('得意先コード')->nullable(false)->change();
            $table->string('customer_place_order_last_code', 8)->comment('発注得意先コード')->nullable(false)->change();
            $table->string('base_plue_end_user_code', 10)->comment('最終顧客コード')->nullable(false)->change();
            $table->unsignedInteger('supplier_place_order_quantity')->comment('仕入先発注数')->nullable(false)->change();
            $table->unsignedInteger('supplier_purchase_compleate_quantity')->comment('仕入先仕入済数')->nullable(false)->change();
            $table->unsignedInteger('supplier_closed_quantity')->comment('仕入先打切数')->nullable(false)->change();
            $table->unsignedInteger('place_order_quantity')->comment('発注数')->nullable(false)->change();
            $table->unsignedInteger('arrival_plan_quantity')->comment('仕入先入荷予定数')->nullable(false)->change();
            $table->unsignedInteger('arrival_compleate_quantity')->comment('入荷済数')->nullable(false)->change();
            $table->unsignedInteger('stock_compleate_quantity')->comment('入庫済数')->nullable(false)->change();
            $table->unsignedInteger('purchase_compleate_quantity')->comment('仕入済数')->nullable(false)->change();
            $table->unsignedInteger('cancellation_quantity')->comment('解約数')->nullable(false)->change();
            $table->unsignedInteger('cancellation_delivery_compleate_quantity')->comment('解約受入済数')->nullable(false)->change();
            $table->string('place_order_unit_code', 3)->comment('発注単位')->nullable(false)->change();
            $table->string('price_number', 15)->comment('価格番号')->nullable(false)->change();
            $table->unsignedDecimal('supplier_place_order_unit', 14, 5)->default(0.00000)->comment('仕入先発注単価')->nullable(false)->change();
            $table->unsignedDecimal('place_order_amount', 16, 3)->default(0.00000)->comment('発注合価')->nullable(false)->change();
            $table->unsignedDecimal('place_order_unit', 14, 5)->default(0.00000)->comment('発注単価')->nullable(false)->change();
            $table->string('delivery_method_class', 1)->comment('納入方法区分')->nullable(false)->change();
            $table->string('stock_rank_class', 1)->comment('在庫ランク区分')->nullable(false)->change();
            $table->string('place_order_message', 50)->comment('発注コメント')->nullable(false)->change();
            $table->string('supplier_instructions', 50)->comment('仕入先指示')->nullable(false)->change();
            $table->string('buyer_remark', 50)->comment('発注者備考')->nullable(false)->change();
            $table->string('delivery_destination_code', 10)->comment('納入先コード')->nullable(false)->change();
            $table->string('warehouse_code', 10)->comment('倉庫コード')->nullable(false)->change();
            $table->string('orders_sheet_issue_date', 8)->comment('注文書発行年月日')->nullable(false)->change();
            $table->string('arrangement_class', 1)->comment('手配区分')->nullable(false)->change();
            $table->string('construction_develop_number', 10)->comment('工事開発番号')->nullable(false)->change();
            $table->string('el_approval_number', 18)->comment('ＥＬ承認番号')->nullable(false)->change();
            $table->string('estimation_approval_number', 15)->comment('見積承認番号')->nullable(false)->change();
            $table->string('supplier_recived_order_number', 14)->comment('仕入先受注番号')->nullable(false)->change();
            $table->string('supplier_original_recived_order_pickup_number', 14)->comment('仕入先元受注伝票番号')->nullable(false)->change();
            $table->string('manufacturing_class', 2)->comment('加工区分')->nullable(false)->change();
            $table->string('inspection_spec_sentence', 20)->comment('検査仕様')->nullable(false)->change();
            $table->string('statement_delivery_date', 8)->comment('納品書年月日')->nullable(false)->change();
            $table->string('set_product_name', 40)->comment('セット品名')->nullable(false)->change();
            $table->string('set_maker', 8)->comment('セット品メーカー')->nullable(false)->change();
            $table->string('set_rank1_code', 3)->comment('セット品規格１')->nullable(false)->change();
            $table->string('set_rank2_code', 3)->comment('セット品規格２')->nullable(false)->change();
            $table->string('set_rank3_code', 3)->comment('セット品規格３')->nullable(false)->change();
            $table->string('set_special_spec_code', 10)->comment('セット品特殊仕様')->nullable(false)->change();
            $table->string('goods_name', 40)->comment('現品名称')->nullable(false)->change();
            $table->unsignedInteger('goods_quantity')->comment('現品数量')->nullable(false)->change();
            $table->string('warehouse_supplier_to_code', 10)->comment('支給先倉庫コード')->nullable(false)->change();
            $table->string('original_place_order_number', 10)->comment('元発注番号')->nullable(false)->change();
            $table->string('original_place_order_chapter_number', 3)->comment('元発注項番')->nullable(false)->change();
            $table->string('original_place_order_delivery_lead_number', 2)->comment('元発注納番')->nullable(false)->change();
            $table->string('original_place_order_branch_number', 2)->comment('元発注枝番')->nullable(false)->change();
            $table->string('approval_number', 12)->comment('承認管理番号')->nullable(false)->change();
            $table->string('reserved_text1', 200)->default('')->comment('予備文字項目1')->nullable(false)->change();
            $table->string('reserved_text2', 200)->default('')->comment('予備文字項目2')->nullable(false)->change();
            $table->string('reserved_text3', 200)->default('')->comment('予備文字項目3')->nullable(false)->change();
            $table->string('reserved_text4', 200)->default('')->comment('予備文字項目4')->nullable(false)->change();
            $table->string('reserved_text5', 200)->default('')->comment('予備文字項目5')->nullable(false)->change();
            $table->string('reserved_text6', 200)->default('')->comment('予備文字項目6')->nullable(false)->change();
            $table->string('reserved_text7', 200)->default('')->comment('予備文字項目7')->nullable(false)->change();
            $table->string('reserved_text8', 200)->default('')->comment('予備文字項目8')->nullable(false)->change();
            $table->string('reserved_text9', 200)->default('')->comment('予備文字項目9')->nullable(false)->change();
            $table->string('reserved_text10', 200)->default('')->comment('予備文字項目10')->nullable(false)->change();
        });
    }
}
