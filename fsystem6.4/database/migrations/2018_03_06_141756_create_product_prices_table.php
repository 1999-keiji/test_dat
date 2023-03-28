<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('product_code', 15)->comment('商品コード');
            $table->string('currency_code', 3)->comment('通貨コード');
            $table->date('application_started_on')->comment('適用開始日');
            $table->unsignedDecimal('unit_price', 14, 5)->default(0.00000)->comment('単価');
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
            $table->boolean('base_plus_delete_flag')->default(false)->comment('BASE+削除フラグ');
            $table->string('base_plus_user_created_by', 8)->default('')->comment('BASE+作成者');
            $table->string('base_plus_program_created_by', 12)->default('')->comment('BASE+作成プログラム');
            $table->datetime('base_plus_created_at')->comment('BASE+作成日時');
            $table->string('base_plus_user_updated_by', 8)->default('')->comment('BASE+更新者');
            $table->string('base_plus_program_updated_by', 12)->default('')->comment('BASE+更新プログラム');
            $table->datetime('base_plus_updated_at')->comment('BASE+更新日時');

            $table->primary(['factory_code', 'product_code', 'currency_code', 'application_started_on'], 'product_prices_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('product_code')->references('product_code')->on('products');
            $table->foreign('currency_code')->references('currency_code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }
}
