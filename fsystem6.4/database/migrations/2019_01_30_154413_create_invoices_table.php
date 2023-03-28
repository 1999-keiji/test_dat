<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->string('invoice_number', 24)->comment('請求書番号');
            $table->string('factory_code', 15)->comment('工場コード');
            $table->string('customer_code', 15)->comment('得意先コード');
            $table->string('delivery_month', 6)->comment('年月');
            $table->string('fixed_by', 15)->nullable()->comment('確定者');
            $table->datetime('fixed_at')->nullable()->comment('確定日時');
            $table->boolean('has_fixed')->default(true)->comment('請求書締めフラグ');
            $table->unsignedInteger('order_quantity')->default(0)->comment('注文件数');
            $table->unsignedInteger('order_amount')->default(0)->comment('合計金額');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary('invoice_number');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
            $table->foreign('customer_code')->references('customer_code')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
