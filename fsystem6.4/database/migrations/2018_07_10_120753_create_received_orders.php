<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivedOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_orders', function (Blueprint $table) {
            $table->string('own_company_code', 6)->default('')->comment('会社コード');
            $table->string('recived_order_number', 10)->default('')->comment('受注番号');
            $table->string('customer_order_number', 23)->default('')->comment('得意先注文番号');
            $table->string('end_user_order_number', 17)->default('')->comment('エンドユーザ注文番号');
            $table->string('pickup_type_class', 1)->default('')->comment('伝票種別区分');
            $table->string('pickup_type_code', 2)->default('')->comment('伝票種別コード');
            $table->string('inter_out_class', 1)->default('')->comment('社内外区分');
            $table->string('export_class', 1)->default('')->comment('貿易区分');
            $table->boolean('lc_trade_flag')->default(null)->nullable()->comment('Ｌ／Ｃ取引フラグ');
            $table->string('lc_number', 30)->default('')->comment('Ｌ／ＣNo.');
            $table->string('basis_for_recording_sales_class', 1)->default('')->comment('売上計上基準区分');
            $table->string('customer_code', 8)->default('')->comment('得意先コード');
            $table->string('delivery_destination_code', 10)->default('')->comment('納入先コード');
            $table->boolean('lease_flag')->default(null)->nullable()->comment('リースフラグ(サイン)');
            $table->string('destination_code', 8)->default('')->comment('請求先コード');
            $table->string('ragistration_means_class', 1)->default('')->comment('登録手段区分');
            $table->string('delivery_key_number_index_class', 1)->default('')->comment('納品キー番号採番区分');
            $table->string('statement_delivery_price_display_class', 1)->default('')->comment('納品書価格表示区分');
            $table->string('statement_delivery_class', 1)->default('')->comment('納品書区分');
            $table->boolean('invoice_issue_flag')->default(null)->nullable()->comment('送り状出力フラグ');
            $table->boolean('oversea_flag')->default(null)->nullable()->comment('海外フラグ');
            $table->string('trade_class', 1)->default('')->comment('取引形態区分');
            $table->string('seller_code', 8)->default('')->comment('販売担当コード');
            $table->string('seller_name', 30)->default('')->comment('販売担当者名');
            $table->string('customer_staff_name', 30)->default('')->comment('得意先担当者名');
            $table->string('suite_name', 30)->default('')->comment('一式名称');
            $table->string('suite_class', 1)->default('')->comment('一式区分');
            $table->unsignedDecimal('estimation_total', 16, 3)->default(0.00000)->comment('見積合価計');
            $table->unsignedDecimal('estimation_cost_total', 16, 3)->default(0.00000)->comment('見積原価計');
            $table->string('suite_statement_delivery_remark', 100)->default('')->comment('一式納品書備考');
            $table->string('tax_class', 1)->default('')->comment('課税区分');
            $table->string('currency_code', 3)->default('')->comment('通貨コード');
            $table->boolean('reserve_flag')->default(null)->nullable()->comment('予約フラグ');
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート');
            $table->boolean('payment_installments_flag')->default(null)->nullable()->comment('分納フラグ');
            $table->boolean('delivery_compleate_flag')->default(null)->nullable()->comment('納入済フラグ');
            $table->boolean('shipment_stop_flag')->default(null)->nullable()->comment('出荷停止フラグ');
            $table->string('free_reason_code', 2)->default('')->comment('無代理由コード');
            $table->string('inter_reason_code', 2)->default('')->comment('社内使用理由コード');
            $table->string('organization_charge_expenses_code', 6)->default('')->comment('経費負担組織コード');
            $table->boolean('blanket_depreciation_flag')->default(null)->nullable()->comment('一括減価フラグ');
            $table->boolean('maintain_period_flag')->default(null)->nullable()->comment('期間保守フラグ');
            $table->string('recived_order_date', 8)->default('')->comment('受注年月日');
            $table->string('reserve_number', 23)->default('')->comment('予約番号');
            $table->string('approval_number', 12)->default('')->comment('承認管理番号');
            $table->string('approval1_staff_code', 8)->default('')->comment('承認1担当者コード');
            $table->string('approval2_staff_code', 8)->default('')->comment('承認2担当者コード');
            $table->string('application_staff_code', 8)->default('')->comment('申請担当者コード');
            $table->boolean('application_approval_during_flag')->default(null)->nullable()->comment('承認申請中フラグ');
            $table->boolean('compleat_flag')->default(null)->nullable()->comment('完了フラグ');
            $table->string('destinations_segment', 10)->default('')->comment('相手先セグメント');
            $table->string('end_user_code', 8)->default('')->comment('エンドユーザコード');
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
        Schema::dropIfExists('received_orders');
    }
}
