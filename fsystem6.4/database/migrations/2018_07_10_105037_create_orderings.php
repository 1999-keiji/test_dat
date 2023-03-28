<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderings', function (Blueprint $table) {
            $table->string('process_class', 1)->comment('処理区分');
            $table->string('own_company_code', 6)->comment('会社コード');
            $table->string('place_order_number', 10)->comment('発注番号');
            $table->string('small_peace_of_peper_type_class', 1)->comment('伝票種別区分');
            $table->string('small_peace_of_peper_type_code', 2)->comment('伝票種別コード');
            $table->string('inter_out_class', 1)->comment('社内外区分');
            $table->string('export_class', 1)->comment('貿易区分');
            $table->string('el_type_code', 2)->comment('ＥＬオーダタイプコード');
            $table->boolean('order_sheet_necessarily_flag')->default(null)->nullable()->comment('注文書必要フラグ');
            $table->string('supplier_flag', 8)->comment('仕入先コード');
            $table->string('ragistration_means_class', 1)->comment('登録手段区分');
            $table->string('tax_class', 1)->comment('課税区分');
            $table->string('purchase_staff_code', 8)->comment('購買担当者コード');
            $table->string('purchase_staff_name', 30)->comment('購買担当者名');
            $table->string('currency_code', 3)->comment('通貨コード');
            $table->boolean('reserve_flag')->default(null)->nullable()->comment('予約フラグ');
            $table->unsignedDecimal('currency_rate', 13, 8)->default(0.00000)->comment('通貨レート');
            $table->string('trade_class', 1)->comment('取引形態区分');
            $table->string('oversea_pay_terms_class', 2)->comment('海外支払条件区分');
            $table->string('trade_terms_class', 2)->comment('取引条件区分');
            $table->string('loading_port_code', 4)->comment('積地コード');
            $table->string('loading_port_name', 20)->comment('積地名');
            $table->string('trade_means_remark', 15)->comment('取引条件備考');
            $table->boolean('maintain_period_flag')->default(null)->nullable()->comment('期間保守フラグ');
            $table->unsignedTinyInteger('place_order_application_approval_class')->default(0)->comment('発注申請承認区分');
            $table->string('approval_number', 12)->comment('承認管理番号');
            $table->string('approval1_staff_code', 8)->comment('承認1担当者コード');
            $table->string('approval2_staff_code', 8)->comment('承認2担当者コード');
            $table->string('application_staff_code', 8)->comment('申請担当者コード');
            $table->boolean('application_approval_during_flag')->default(null)->nullable()->comment('承認申請中フラグ');
            $table->string('supplier_staff_name', 30)->comment('仕入先担当者名');
            $table->string('place_order_work_staff_code', 8)->comment('発注業務担当者コード');
            $table->string('place_order_work_staff_name', 30)->comment('発注業務担当名');
            $table->string('place_order_application_number', 10)->comment('発注申請番号');
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
        Schema::dropIfExists('orderings');
    }
}
