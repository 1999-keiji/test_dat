<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReceivedOrdersChangeNullRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('received_orders', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード')->nullable()->change();
            $table->string('recived_order_number', 10)->default('')->comment('受注番号')->nullable()->change();
            $table->string('customer_order_number', 23)->default('')->comment('得意先注文番号')->nullable()->change();
            $table->string('end_user_order_number', 17)->default('')->comment('エンドユーザ注文番号')->nullable()->change();
            $table->string('pickup_type_class', 1)->default('')->comment('伝票種別区分')->nullable()->change();
            $table->string('pickup_type_code', 2)->default('')->comment('伝票種別コード')->nullable()->change();
            $table->string('inter_out_class', 1)->default('')->comment('社内外区分')->nullable()->change();
            $table->string('export_class', 1)->default('')->comment('貿易区分')->nullable()->change();
            $table->string('lc_number', 30)->default('')->comment('Ｌ／ＣNo.')->nullable()->change();
            $table->string('basis_for_recording_sales_class', 1)->default('')->comment('売上計上基準区分')->nullable()->change();
            $table->string('customer_code', 8)->default('')->comment('得意先コード')->nullable()->change();
            $table->string('delivery_destination_code', 10)->default('')->comment('納入先コード')->nullable()->change();
            $table->string('destination_code', 8)->default('')->comment('請求先コード')->nullable()->change();
            $table->string('ragistration_means_class', 1)->default('')->comment('登録手段区分')->nullable()->change();
            $table->string('delivery_key_number_index_class', 1)->default('')->comment('納品キー番号採番区分')->nullable()->change();
            $table->string('statement_delivery_price_display_class', 1)->default('')->comment('納品書価格表示区分')->nullable()->change();
            $table->string('statement_delivery_class', 1)->default('')->comment('納品書区分')->nullable()->change();
            $table->string('trade_class', 1)->default('')->comment('取引形態区分')->nullable()->change();
            $table->string('seller_code', 8)->default('')->comment('販売担当コード')->nullable()->change();
            $table->string('seller_name', 30)->default('')->comment('販売担当者名')->nullable()->change();
            $table->string('customer_staff_name', 30)->default('')->comment('得意先担当者名')->nullable()->change();
            $table->string('suite_name', 30)->default('')->comment('一式名称')->nullable()->change();
            $table->string('suite_class', 1)->default('')->comment('一式区分')->nullable()->change();
            $table->unsignedDecimal('estimation_total', 16, 3)->default(0.00000)->comment('見積合価計')->nullable()->change();
            $table->unsignedDecimal('estimation_cost_total', 16, 3)->default(0.00000)->comment('見積原価計')->nullable()->change();
            $table->string('suite_statement_delivery_remark', 100)->default('')->comment('一式納品書備考')->nullable()->change();
            $table->string('tax_class', 1)->default('')->comment('課税区分')->nullable()->change();
            $table->string('currency_code', 3)->default('')->comment('通貨コード')->nullable()->change();
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート')->nullable()->change();
            $table->string('free_reason_code', 2)->default('')->comment('無代理由コード')->nullable()->change();
            $table->string('inter_reason_code', 2)->default('')->comment('社内使用理由コード')->nullable()->change();
            $table->string('organization_charge_expenses_code', 6)->default('')->comment('経費負担組織コード')->nullable()->change();
            $table->string('recived_order_date', 8)->default('')->comment('受注年月日')->nullable()->change();
            $table->string('reserve_number', 23)->default('')->comment('予約番号')->nullable()->change();
            $table->string('approval_number', 12)->default('')->comment('承認管理番号')->nullable()->change();
            $table->string('approval1_staff_code', 8)->default('')->comment('承認1担当者コード')->nullable()->change();
            $table->string('approval2_staff_code', 8)->default('')->comment('承認2担当者コード')->nullable()->change();
            $table->string('application_staff_code', 8)->default('')->comment('申請担当者コード')->nullable()->change();
            $table->string('destinations_segment', 10)->default('')->comment('相手先セグメント')->nullable()->change();
            $table->string('end_user_code', 8)->default('')->comment('エンドユーザコード')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('received_orders', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード')->nullable(false)->change();
            $table->string('recived_order_number', 10)->default('')->comment('受注番号')->nullable(false)->change();
            $table->string('customer_order_number', 23)->default('')->comment('得意先注文番号')->nullable(false)->change();
            $table->string('end_user_order_number', 17)->default('')->comment('エンドユーザ注文番号')->nullable(false)->change();
            $table->string('pickup_type_class', 1)->default('')->comment('伝票種別区分')->nullable(false)->change();
            $table->string('pickup_type_code', 2)->default('')->comment('伝票種別コード')->nullable(false)->change();
            $table->string('inter_out_class', 1)->default('')->comment('社内外区分')->nullable(false)->change();
            $table->string('export_class', 1)->default('')->comment('貿易区分')->nullable(false)->change();
            $table->string('lc_number', 30)->default('')->comment('Ｌ／ＣNo.')->nullable(false)->change();
            $table->string('basis_for_recording_sales_class', 1)->default('')->comment('売上計上基準区分')->nullable(false)->change();
            $table->string('customer_code', 8)->default('')->comment('得意先コード')->nullable(false)->change();
            $table->string('delivery_destination_code', 10)->default('')->comment('納入先コード')->nullable(false)->change();
            $table->string('destination_code', 8)->default('')->comment('請求先コード')->nullable(false)->change();
            $table->string('ragistration_means_class', 1)->default('')->comment('登録手段区分')->nullable(false)->change();
            $table->string('delivery_key_number_index_class', 1)->default('')->comment('納品キー番号採番区分')->nullable(false)->change();
            $table->string('statement_delivery_price_display_class', 1)->default('')->comment('納品書価格表示区分')->nullable(false)->change();
            $table->string('statement_delivery_class', 1)->default('')->comment('納品書区分')->nullable(false)->change();
            $table->string('trade_class', 1)->default('')->comment('取引形態区分')->nullable(false)->change();
            $table->string('seller_code', 8)->default('')->comment('販売担当コード')->nullable(false)->change();
            $table->string('seller_name', 30)->default('')->comment('販売担当者名')->nullable(false)->change();
            $table->string('customer_staff_name', 30)->default('')->comment('得意先担当者名')->nullable(false)->change();
            $table->string('suite_name', 30)->default('')->comment('一式名称')->nullable(false)->change();
            $table->string('suite_class', 1)->default('')->comment('一式区分')->nullable(false)->change();
            $table->unsignedDecimal('estimation_total', 16, 3)->default(0.00000)->comment('見積合価計')->nullable(false)->change();
            $table->unsignedDecimal('estimation_cost_total', 16, 3)->default(0.00000)->comment('見積原価計')->nullable(false)->change();
            $table->string('suite_statement_delivery_remark', 100)->default('')->comment('一式納品書備考')->nullable(false)->change();
            $table->string('tax_class', 1)->default('')->comment('課税区分')->nullable(false)->change();
            $table->string('currency_code', 3)->default('')->comment('通貨コード')->nullable(false)->change();
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート')->nullable(false)->change();
            $table->string('free_reason_code', 2)->default('')->comment('無代理由コード')->nullable(false)->change();
            $table->string('inter_reason_code', 2)->default('')->comment('社内使用理由コード')->nullable(false)->change();
            $table->string('organization_charge_expenses_code', 6)->default('')->comment('経費負担組織コード')->nullable(false)->change();
            $table->string('recived_order_date', 8)->default('')->comment('受注年月日')->nullable(false)->change();
            $table->string('reserve_number', 23)->default('')->comment('予約番号')->nullable(false)->change();
            $table->string('approval_number', 12)->default('')->comment('承認管理番号')->nullable(false)->change();
            $table->string('approval1_staff_code', 8)->default('')->comment('承認1担当者コード')->nullable(false)->change();
            $table->string('approval2_staff_code', 8)->default('')->comment('承認2担当者コード')->nullable(false)->change();
            $table->string('application_staff_code', 8)->default('')->comment('申請担当者コード')->nullable(false)->change();
            $table->string('destinations_segment', 10)->default('')->comment('相手先セグメント')->nullable(false)->change();
            $table->string('end_user_code', 8)->default('')->comment('エンドユーザコード')->nullable(false)->change();
        });
    }
}
