<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesAddColumnPrintingShippingDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->date('printing_shipping_date')->nullable()->comment('帳票用出荷日')->after('shipping_date');
        });

        DB::statement('UPDATE order_histories SET printing_shipping_date = shipping_date');

        Schema::table('order_histories', function (Blueprint $table) {
            $table->date('printing_shipping_date')->comment('帳票用出荷日')->change();
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
            $table->dropColumn('printing_shipping_date');
        });
    }
}
