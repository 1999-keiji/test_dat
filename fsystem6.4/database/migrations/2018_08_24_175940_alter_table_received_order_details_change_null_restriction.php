<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReceivedOrderDetailsChangeNullRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('received_order_details', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード')->nullable()->change();
            $table->string('recived_order_number', 10)->default('')->comment('受注番号')->nullable()->change();
            $table->string('recived_order_chapter_number', 3)->default('')->comment('受注項番')->nullable()->change();
            $table->string('original_place_order_number', 10)->default('')->comment('元受注番号')->nullable()->change();
            $table->string('original_place_order_chapter_number', 3)->default('')->comment('元受注項番')->nullable()->change();
            $table->string('original_place_order_delivery_lead_number', 2)->default('')->comment('元受注納番')->nullable()->change();
            $table->string('original_place_order_branch_number', 2)->default('')->comment('元受注枝番')->nullable()->change();
            $table->string('recived_order_status_code', 3)->default('')->comment('受注ステータスコード')->nullable()->change();
            $table->string('recived_order_annulment_reason_code', 2)->default('')->comment('受注取消理由コード')->nullable()->change();
            $table->string('free_recived_order_class', 1)->default('')->comment('無代指定区分')->nullable()->change();
            $table->string('prodcut_class', 1)->default('')->comment('製品区分')->nullable()->change();
            $table->string('customer_product_name', 50)->default('')->comment('得意先品名')->nullable()->change();
            $table->string('product_name', 40)->default('')->comment('品名')->nullable()->change();
            $table->string('recived_place_order_rank1_code', 3)->default('')->comment('受注規格1')->nullable()->change();
            $table->string('recived_place_order_rank2_code', 3)->default('')->comment('受注規格2')->nullable()->change();
            $table->string('recived_place_order_rank3_code', 3)->default('')->comment('受注規格3')->nullable()->change();
            $table->string('saller_rank_code', 12)->default('')->comment('営業規格')->nullable()->change();
            $table->string('special_spec_code', 10)->default('')->comment('特殊仕様')->nullable()->change();
            $table->string('maker_code', 8)->default('')->comment('メーカーコード')->nullable()->change();
            $table->string('product_number', 15)->default('')->comment('品番')->nullable()->change();
            $table->string('requestor_code', 10)->default('')->comment('要求元コード')->nullable()->change();
            $table->string('requestor_organization_code', 6)->default('')->comment('要求元組織コード')->nullable()->change();
            $table->string('organization_name', 40)->default('')->comment('組織名')->nullable()->change();
            $table->string('stock_class', 1)->default('')->comment('在庫区分')->nullable()->change();
            $table->string('inspection_spec_sentence', 220)->default('')->comment('検査仕様')->nullable()->change();
            $table->string('attached_item', 230)->default('')->comment('添付品')->nullable()->change();
            $table->unsignedInteger('customer_recived_order_quantity')->comment('得意先受注数')->nullable()->change();
            $table->unsignedInteger('customer_sales_compleate_quantity')->comment('得意先売上済数')->nullable()->change();
            $table->unsignedInteger('customer_closed_quantity')->comment('得意先打切数')->nullable()->change();
            $table->unsignedInteger('recived_order_quantity')->comment('受注数')->nullable()->change();
            $table->unsignedInteger('recived_order_unallocated_quantity')->comment('受注未引当数')->nullable()->change();
            $table->unsignedInteger('recived_order_allocate_compleate_quantity')->comment('受注引当済数')->nullable()->change();
            $table->unsignedInteger('recived_order_goods_issue_quantity')->comment('受注出庫指示済数')->nullable()->change();
            $table->unsignedInteger('recived_order_goods_issue_pickup_compleate_quantity')->comment('受注出庫伝票発行済数')->nullable()->change();
            $table->unsignedInteger('recived_order_goods_issue_compleate_quantity')->comment('受注出庫済数')->nullable()->change();
            $table->unsignedInteger('recived_order_shipment_compleate_quantity')->comment('受注出荷済数')->nullable()->change();
            $table->unsignedInteger('recived_order_sales_compleate_quantity')->comment('受注売上済数')->nullable()->change();
            $table->string('recived_order_unit', 3)->default('')->comment('受注単位')->nullable()->change();
            $table->string('stock_unit', 3)->default('')->comment('在庫単位')->nullable()->change();
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->default(0.00000)->comment('得意先受注単価')->nullable()->change();
            $table->unsignedDecimal('recived_order_unit_amount', 14, 5)->default(0.00000)->comment('受注単価')->nullable()->change();
            $table->unsignedDecimal('customer_recived_order_total', 16, 3)->default(0.00000)->comment('得意先受注合価')->nullable()->change();
            $table->unsignedDecimal('invoice_display_unit', 14, 5)->default(0.00000)->comment('インボイス表示単価')->nullable()->change();
            $table->unsignedDecimal('invoice_display_total', 16, 3)->default(0.00000)->comment('インボイス表示合価')->nullable()->change();
            $table->string('allocated_class', 1)->default('')->comment('引当区分')->nullable()->change();
            $table->string('delivery_way_class', 1)->default('')->comment('配送方法区分')->nullable()->change();
            $table->string('saller_instructions', 50)->default('')->comment('営業指示')->nullable()->change();
            $table->string('recived_order_person_remark', 50)->default('')->comment('受注者用備考')->nullable()->change();
            $table->string('buyer_remark', 50)->default('')->comment('発注者備考')->nullable()->change();
            $table->string('buyer_barcode_information', 23)->default('')->comment('発注者用バーコード情報')->nullable()->change();
            $table->string('research_development_product_number', 10)->default('')->comment('研究開発製造番号')->nullable()->change();
            $table->string('goods_name', 40)->default('')->comment('現品名称')->nullable()->change();
            $table->unsignedInteger('goods_quantity')->comment('現品数量')->nullable()->change();
            $table->string('approval_number', 12)->default('')->comment('承認管理番号')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('received_order_details', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード')->nullable(false)->change();
            $table->string('recived_order_number', 10)->default('')->comment('受注番号')->nullable(false)->change();
            $table->string('recived_order_chapter_number', 3)->default('')->comment('受注項番')->nullable(false)->change();
            $table->string('original_place_order_number', 10)->default('')->comment('元受注番号')->nullable(false)->change();
            $table->string('original_place_order_chapter_number', 3)->default('')->comment('元受注項番')->nullable(false)->change();
            $table->string('original_place_order_delivery_lead_number', 2)->default('')->comment('元受注納番')->nullable(false)->change();
            $table->string('original_place_order_branch_number', 2)->default('')->comment('元受注枝番')->nullable(false)->change();
            $table->string('recived_order_status_code', 3)->default('')->comment('受注ステータスコード')->nullable(false)->change();
            $table->string('recived_order_annulment_reason_code', 2)->default('')->comment('受注取消理由コード')->nullable(false)->change();
            $table->string('free_recived_order_class', 1)->default('')->comment('無代指定区分')->nullable(false)->change();
            $table->string('prodcut_class', 1)->default('')->comment('製品区分')->nullable(false)->change();
            $table->string('customer_product_name', 50)->default('')->comment('得意先品名')->nullable(false)->change();
            $table->string('product_name', 40)->default('')->comment('品名')->nullable(false)->change();
            $table->string('recived_place_order_rank1_code', 3)->default('')->comment('受注規格1')->nullable(false)->change();
            $table->string('recived_place_order_rank2_code', 3)->default('')->comment('受注規格2')->nullable(false)->change();
            $table->string('recived_place_order_rank3_code', 3)->default('')->comment('受注規格3')->nullable(false)->change();
            $table->string('saller_rank_code', 12)->default('')->comment('営業規格')->nullable(false)->change();
            $table->string('special_spec_code', 10)->default('')->comment('特殊仕様')->nullable(false)->change();
            $table->string('maker_code', 8)->default('')->comment('メーカーコード')->nullable(false)->change();
            $table->string('product_number', 15)->default('')->comment('品番')->nullable(false)->change();
            $table->string('requestor_code', 10)->default('')->comment('要求元コード')->nullable(false)->change();
            $table->string('requestor_organization_code', 6)->default('')->comment('要求元組織コード')->nullable(false)->change();
            $table->string('organization_name', 40)->default('')->comment('組織名')->nullable(false)->change();
            $table->string('stock_class', 1)->default('')->comment('在庫区分')->nullable(false)->change();
            $table->string('inspection_spec_sentence', 220)->default('')->comment('検査仕様')->nullable(false)->change();
            $table->string('attached_item', 230)->default('')->comment('添付品')->nullable(false)->change();
            $table->unsignedInteger('customer_recived_order_quantity')->comment('得意先受注数')->nullable(false)->change();
            $table->unsignedInteger('customer_sales_compleate_quantity')->comment('得意先売上済数')->nullable(false)->change();
            $table->unsignedInteger('customer_closed_quantity')->comment('得意先打切数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_quantity')->comment('受注数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_unallocated_quantity')->comment('受注未引当数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_allocate_compleate_quantity')->comment('受注引当済数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_goods_issue_quantity')->comment('受注出庫指示済数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_goods_issue_pickup_compleate_quantity')->comment('受注出庫伝票発行済数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_goods_issue_compleate_quantity')->comment('受注出庫済数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_shipment_compleate_quantity')->comment('受注出荷済数')->nullable(false)->change();
            $table->unsignedInteger('recived_order_sales_compleate_quantity')->comment('受注売上済数')->nullable(false)->change();
            $table->string('recived_order_unit', 3)->default('')->comment('受注単位')->nullable(false)->change();
            $table->string('stock_unit', 3)->default('')->comment('在庫単位')->nullable(false)->change();
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->default(0.00000)->comment('得意先受注単価')->nullable(false)->change();
            $table->unsignedDecimal('recived_order_unit_amount', 14, 5)->default(0.00000)->comment('受注単価')->nullable(false)->change();
            $table->unsignedDecimal('customer_recived_order_total', 16, 3)->default(0.00000)->comment('得意先受注合価')->nullable(false)->change();
            $table->unsignedDecimal('invoice_display_unit', 14, 5)->default(0.00000)->comment('インボイス表示単価')->nullable(false)->change();
            $table->unsignedDecimal('invoice_display_total', 16, 3)->default(0.00000)->comment('インボイス表示合価')->nullable(false)->change();
            $table->string('allocated_class', 1)->default('')->comment('引当区分')->nullable(false)->change();
            $table->string('delivery_way_class', 1)->default('')->comment('配送方法区分')->nullable(false)->change();
            $table->string('saller_instructions', 50)->default('')->comment('営業指示')->nullable(false)->change();
            $table->string('recived_order_person_remark', 50)->default('')->comment('受注者用備考')->nullable(false)->change();
            $table->string('buyer_remark', 50)->default('')->comment('発注者備考')->nullable(false)->change();
            $table->string('buyer_barcode_information', 23)->default('')->comment('発注者用バーコード情報')->nullable(false)->change();
            $table->string('research_development_product_number', 10)->default('')->comment('研究開発製造番号')->nullable(false)->change();
            $table->string('goods_name', 40)->default('')->comment('現品名称')->nullable(false)->change();
            $table->unsignedInteger('goods_quantity')->comment('現品数量')->nullable(false)->change();
            $table->string('approval_number', 12)->default('')->comment('承認管理番号')->nullable(false)->change();
        });
    }
}
