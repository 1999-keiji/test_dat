<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoriesAddInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->string('bank_name', 40)->comment('銀行名')->after('supplier_code');
            $table->string('bank_branch_name', 40)->comment('銀行支店名')->after('bank_name');
            $table->string('bank_account_number', 8)->comment('口座番号')->after('bank_branch_name');
            $table->string('bank_account_holder', 40)->comment('振込先名義')->after('bank_account_number');
            $table->string('collection_staff_name', 30)->comment('集荷案内担当者')->after('symbolic_code');
            $table->string('invoice_bank_name', 40)->comment('請求書銀行名')->after('invoice_fax_number');
            $table->string('invoice_bank_branch_name', 40)->comment('請求書銀行支店名')->after('invoice_bank_name');
            $table->string('invoice_bank_account_number', 8)->comment('請求書口座番号')->after('invoice_bank_branch_name');
            $table->string('invoice_bank_account_holder', 40)->comment('請求書振込先名義')
                ->after('invoice_bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->dropColumn('bank_name');
            $table->dropColumn('bank_branch_name');
            $table->dropColumn('bank_account_number');
            $table->dropColumn('bank_account_holder');
            $table->dropColumn('collection_staff_name');
            $table->dropColumn('invoice_bank_name');
            $table->dropColumn('invoice_bank_branch_name');
            $table->dropColumn('invoice_bank_account_number');
            $table->dropColumn('invoice_bank_account_holder');
        });
    }
}
