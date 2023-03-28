<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivedOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_order_details', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード');
            $table->string('recived_order_number', 10)->default('')->comment('受注番号');
            $table->string('recived_order_chapter_number', 3)->default('')->comment('受注項番');
            $table->string('original_place_order_number', 10)->default('')->comment('元受注番号');
            $table->string('original_place_order_chapter_number', 3)->default('')->comment('元受注項番');
            $table->string('original_place_order_delivery_lead_number', 2)->default('')->comment('元受注納番');
            $table->string('original_place_order_branch_number', 2)->default('')->comment('元受注枝番');
            $table->string('recived_order_status_code', 3)->default('')->comment('受注ステータスコード');
            $table->string('recived_order_annulment_reason_code', 2)->default('')->comment('受注取消理由コード');
            $table->string('free_recived_order_class', 1)->default('')->comment('無代指定区分');
            $table->string('prodcut_class', 1)->default('')->comment('製品区分');
            $table->string('customer_product_name', 50)->default('')->comment('得意先品名');
            $table->string('product_name', 40)->default('')->comment('品名');
            $table->string('recived_place_order_rank1_code', 3)->default('')->comment('受注規格1');
            $table->string('recived_place_order_rank2_code', 3)->default('')->comment('受注規格2');
            $table->string('recived_place_order_rank3_code', 3)->default('')->comment('受注規格3');
            $table->string('saller_rank_code', 12)->default('')->comment('営業規格');
            $table->string('special_spec_code', 10)->default('')->comment('特殊仕様');
            $table->string('maker_code', 8)->default('')->comment('メーカーコード');
            $table->string('product_number', 15)->default('')->comment('品番');
            $table->string('requestor_code', 10)->default('')->comment('要求元コード');
            $table->string('requestor_organization_code', 6)->default('')->comment('要求元組織コード');
            $table->string('organization_name', 40)->default('')->comment('組織名');
            $table->string('stock_class', 1)->default('')->comment('在庫区分');
            $table->string('inspection_spec_sentence', 220)->default('')->comment('検査仕様');
            $table->string('attached_item', 230)->default('')->comment('添付品');
            $table->boolean('out_of_product_linked_compleate_flag')->default(null)->nullable()->comment('製品外紐付済フラグ');
            $table->boolean('detail_payment_installments_flag')->default(null)->nullable()->comment('明細分納フラグ');
            $table->boolean('direct_shipment_flag')->default(null)->nullable()->comment('直送フラグ');
            $table->boolean('minimum_recived_order_flag')->default(null)->nullable()->comment('最小受注フラグ');
            $table->boolean('immediately_shipment_flag')->default(null)->nullable()->comment('即出荷フラグ');
            $table->boolean('recived_order_unit_flag')->default(null)->nullable()->comment('受注単位フラグ');
            $table->boolean('unit_release_flag')->default(null)->nullable()->comment('単価洗い替えフラグ');
            $table->boolean('linked_place_order_flag')->default(null)->nullable()->comment('紐付発注フラグ');
            $table->boolean('placing_recived_flag')->default(null)->nullable()->comment('受発注品フラグ');
            $table->unsignedInteger('customer_recived_order_quantity')->comment('得意先受注数');
            $table->unsignedInteger('customer_sales_compleate_quantity')->comment('得意先売上済数');
            $table->unsignedInteger('customer_closed_quantity')->comment('得意先打切数');
            $table->unsignedInteger('recived_order_quantity')->comment('受注数');
            $table->unsignedInteger('recived_order_unallocated_quantity')->comment('受注未引当数');
            $table->unsignedInteger('recived_order_allocate_compleate_quantity')->comment('受注引当済数');
            $table->unsignedInteger('recived_order_goods_issue_quantity')->comment('受注出庫指示済数');
            $table->unsignedInteger('recived_order_goods_issue_pickup_compleate_quantity')->comment('受注出庫伝票発行済数');
            $table->unsignedInteger('recived_order_goods_issue_compleate_quantity')->comment('受注出庫済数');
            $table->unsignedInteger('recived_order_shipment_compleate_quantity')->comment('受注出荷済数');
            $table->unsignedInteger('recived_order_sales_compleate_quantity')->comment('受注売上済数');
            $table->string('recived_order_unit', 3)->default('')->comment('受注単位');
            $table->string('stock_unit', 3)->default('')->comment('在庫単位');
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->default(0.00000)->comment('得意先受注単価');
            $table->unsignedDecimal('recived_order_unit_amount', 14, 5)->default(0.00000)->comment('受注単価');
            $table->unsignedDecimal('customer_recived_order_total', 16, 3)->default(0.00000)->comment('得意先受注合価');
            $table->unsignedDecimal('invoice_display_unit', 14, 5)->default(0.00000)->comment('インボイス表示単価');
            $table->unsignedDecimal('invoice_display_total', 16, 3)->default(0.00000)->comment('インボイス表示合価');
            $table->string('allocated_class', 1)->default('')->comment('引当区分');
            $table->string('delivery_way_class', 1)->default('')->comment('配送方法区分');
            $table->string('saller_instructions', 50)->default('')->comment('営業指示');
            $table->string('recived_order_person_remark', 50)->default('')->comment('受注者用備考');
            $table->string('buyer_remark', 50)->default('')->comment('発注者備考');
            $table->string('buyer_barcode_information', 23)->default('')->comment('発注者用バーコード情報');
            $table->string('research_development_product_number', 10)->default('')->comment('研究開発製造番号');
            $table->boolean('auto_inter_place_order_extraction_compleate_flag')->default(null)->nullable()->comment('自動社内発注抽出済フラグ');
            $table->boolean('repair_order_flag')->default(null)->nullable()->comment('修理オーダフラグ');
            $table->string('goods_name', 40)->default('')->comment('現品名称');
            $table->unsignedInteger('goods_quantity')->comment('現品数量');
            $table->string('approval_number', 12)->default('')->comment('承認管理番号');
            $table->boolean('compleat_flag')->default(null)->nullable()->comment('完了フラグ');
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('received_order_details');
    }
}
