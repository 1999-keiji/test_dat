<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesAddColumnInvoiceNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->string('invoice_number', 24)->nullable()->comment('請求書番号')->after('fixed_shipping_sharing_flag');
            $table->foreign('invoice_number')->references('invoice_number')->on('invoices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->dropForeign('order_histories_invoice_number_foreign');
            $table->dropColumn('invoice_number');
        });
    }
}
