<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderingsChangeNullRestriction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orderings', function (Blueprint $table) {
            $table->string('process_class', 1)->comment('処理区分')->nullable()->change();
            $table->string('own_company_code', 6)->comment('会社コード')->nullable()->change();
            $table->string('small_peace_of_peper_type_class', 1)->comment('伝票種別区分')->nullable()->change();
            $table->string('small_peace_of_peper_type_code', 2)->comment('伝票種別コード')->nullable()->change();
            $table->string('inter_out_class', 1)->comment('社内外区分')->nullable()->change();
            $table->string('export_class', 1)->comment('貿易区分')->nullable()->change();
            $table->string('el_type_code', 2)->comment('ＥＬオーダタイプコード')->nullable()->change();
            $table->string('supplier_flag', 8)->comment('仕入先コード')->nullable()->change();
            $table->string('ragistration_means_class', 1)->comment('登録手段区分')->nullable()->change();
            $table->string('tax_class', 1)->comment('課税区分')->nullable()->change();
            $table->string('purchase_staff_code', 8)->comment('購買担当者コード')->nullable()->change();
            $table->string('purchase_staff_name', 30)->comment('購買担当者名')->nullable()->change();
            $table->string('currency_code', 3)->comment('通貨コード')->nullable()->change();
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート')->nullable()->change();
            $table->string('trade_class', 1)->comment('取引形態区分')->nullable()->change();
            $table->string('oversea_pay_terms_class', 2)->comment('海外支払条件区分')->nullable()->change();
            $table->string('trade_terms_class', 2)->comment('取引条件区分')->nullable()->change();
            $table->string('loading_port_code', 4)->comment('積地コード')->nullable()->change();
            $table->string('loading_port_name', 20)->comment('積地名')->nullable()->change();
            $table->string('trade_means_remark', 15)->comment('取引条件備考')->nullable()->change();
            $table->string('approval_number', 12)->comment('承認管理番号')->nullable()->change();
            $table->string('approval1_staff_code', 8)->comment('承認1担当者コード')->nullable()->change();
            $table->string('approval2_staff_code', 8)->comment('承認2担当者コード')->nullable()->change();
            $table->string('application_staff_code', 8)->comment('申請担当者コード')->nullable()->change();
            $table->string('supplier_staff_name', 30)->comment('仕入先担当者名')->nullable()->change();
            $table->string('place_order_work_staff_code', 8)->comment('発注業務担当者コード')->nullable()->change();
            $table->string('place_order_work_staff_name', 30)->comment('発注業務担当名')->nullable()->change();
            $table->string('place_order_application_number', 10)->comment('発注申請番号')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orderings', function (Blueprint $table) {
            $table->string('process_class', 1)->comment('処理区分')->nullable(false)->change();
            $table->string('own_company_code', 6)->comment('会社コード')->nullable(false)->change();
            $table->string('small_peace_of_peper_type_class', 1)->comment('伝票種別区分')->nullable(false)->change();
            $table->string('small_peace_of_peper_type_code', 2)->comment('伝票種別コード')->nullable(false)->change();
            $table->string('inter_out_class', 1)->comment('社内外区分')->nullable(false)->change();
            $table->string('export_class', 1)->comment('貿易区分')->nullable(false)->change();
            $table->string('el_type_code', 2)->comment('ＥＬオーダタイプコード')->nullable(false)->change();
            $table->string('supplier_flag', 8)->comment('仕入先コード')->nullable(false)->change();
            $table->string('ragistration_means_class', 1)->comment('登録手段区分')->nullable(false)->change();
            $table->string('tax_class', 1)->comment('課税区分')->nullable(false)->change();
            $table->string('purchase_staff_code', 8)->comment('購買担当者コード')->nullable(false)->change();
            $table->string('purchase_staff_name', 30)->comment('購買担当者名')->nullable(false)->change();
            $table->string('currency_code', 3)->comment('通貨コード')->nullable(false)->change();
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート')->nullable(false)->change();
            $table->string('trade_class', 1)->comment('取引形態区分')->nullable(false)->change();
            $table->string('oversea_pay_terms_class', 2)->comment('海外支払条件区分')->nullable(false)->change();
            $table->string('trade_terms_class', 2)->comment('取引条件区分')->nullable(false)->change();
            $table->string('loading_port_code', 4)->comment('積地コード')->nullable(false)->change();
            $table->string('loading_port_name', 20)->comment('積地名')->nullable(false)->change();
            $table->string('trade_means_remark', 15)->comment('取引条件備考')->nullable(false)->change();
            $table->string('approval_number', 12)->comment('承認管理番号')->nullable(false)->change();
            $table->string('approval1_staff_code', 8)->comment('承認1担当者コード')->nullable(false)->change();
            $table->string('approval2_staff_code', 8)->comment('承認2担当者コード')->nullable(false)->change();
            $table->string('application_staff_code', 8)->comment('申請担当者コード')->nullable(false)->change();
            $table->string('supplier_staff_name', 30)->comment('仕入先担当者名')->nullable(false)->change();
            $table->string('place_order_work_staff_code', 8)->comment('発注業務担当者コード')->nullable(false)->change();
            $table->string('place_order_work_staff_name', 30)->comment('発注業務担当名')->nullable(false)->change();
            $table->string('place_order_application_number', 10)->comment('発注申請番号')->nullable(false)->change();
        });
    }
}
