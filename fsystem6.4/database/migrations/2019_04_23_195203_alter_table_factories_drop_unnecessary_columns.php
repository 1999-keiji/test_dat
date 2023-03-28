<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoriesDropUnnecessaryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->dropColumn([
                'collection_staff_name',
                'collection_guide_message',
                'collection_guide_message2',
                'bank_name',
                'bank_branch_name',
                'bank_account_number',
                'bank_account_holder'
            ]);
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
            $table->string('collection_staff_name', 30)->comment('集荷案内担当者')->after('symbolic_code');
            $table->string('collection_guide_message', 255)
                ->comment('集荷案内メッセージ1')->after('collection_staff_name');
            $table->string('collection_guide_message2', 255)
                ->comment('集荷案内メッセージ2')->after('collection_guide_message');
            $table->string('bank_name', 40)->comment('銀行名')->after('supplier_code');
            $table->string('bank_branch_name', 40)->comment('銀行支店名')->after('bank_name');
            $table->string('bank_account_number', 8)->comment('口座番号')->after('bank_branch_name');
            $table->string('bank_account_holder', 40)->comment('振込先名義')->after('bank_account_number');
        });
    }
}
