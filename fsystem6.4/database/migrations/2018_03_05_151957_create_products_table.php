<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProductClass;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_code', 15)->comment('商品コード');
            $table->string('species_code', 15)->comment('品種コード');
            $table->unsignedTinyInteger('creating_type')->default(CreatingType::MANUAL_CREATED)->comment('登録種別');
            $table->string('product_name', 40)->default('')->comment('商品名');
            $table->string('result_addup_code', 10)->default('')->comment('実績集計コード');
            $table->string('result_addup_name', 30)->default('')->comment('実績集計名称');
            $table->string('result_addup_abbreviation', 10)->default('')->comment('実績集計略称');
            $table->string('product_large_category', 3)->default('')->comment('商品大カテゴリ');
            $table->string('product_middle_category', 3)->default('')->comment('商品中カテゴリ');
            $table->string('product_class', 1)->default(ProductClass::PRODUCT)->comment('製品区分');
            $table->boolean('custom_product_flag')->default(false)->comment('カスタム品フラグ');
            $table->string('sales_order_unit', 3)->default('')->comment('受注単位');
            $table->unsignedInteger('sales_order_unit_quantity')->default(0)->comment('受注単位数');
            $table->unsignedInteger('minimum_sales_order_unit_quantity')->default(0)->comment('最低受注数');
            $table->string('statement_of_delivery_name', 50)->default('')->comment('納品書品名');
            $table->string('pickup_slip_message', 40)->default('')->comment('出庫伝票コメント');
            $table->boolean('lot_target_flag')->default(false)->comment('ロット採取対象フラグ');
            $table->string('species_name', 25)->default('')->comment('品種名');
            $table->boolean('export_target_flag')->default(false)->comment('輸出対象フラグ');
            $table->unsignedDecimal('net_weight', 9, 3)->default(0.000)->comment('純重量');
            $table->unsignedDecimal('gross_weight', 9, 3)->default(0.000)->comment('総重量');
            $table->unsignedDecimal('depth', 7, 2)->default(0.00)->comment('縦サイズ');
            $table->unsignedDecimal('width', 7, 2)->default(0.00)->comment('横サイズ');
            $table->unsignedDecimal('height', 7, 2)->default(0.00)->comment('縦サイズ');
            $table->string('country_of_origin', 2)->default('')->comment('原産国');
            $table->string('itm_class2', 2)->default('')->comment('汎用区分2');
            $table->string('itm_class3', 2)->default('')->comment('汎用区分3');
            $table->string('itm_class4', 2)->default('')->comment('汎用区分4');
            $table->string('itm_class5', 2)->default('')->comment('汎用区分5');
            $table->boolean('itm_flag1')->default(false)->comment('汎用フラグ1');
            $table->boolean('itm_flag2')->default(false)->comment('汎用フラグ2');
            $table->boolean('itm_flag3')->default(false)->comment('汎用フラグ3');
            $table->boolean('itm_flag4')->default(false)->comment('汎用フラグ4');
            $table->boolean('itm_flag5')->default(false)->comment('汎用フラグ5');
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

            $table->primary('product_code');
            $table->foreign('species_code')->references('species_code')->on('species');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
