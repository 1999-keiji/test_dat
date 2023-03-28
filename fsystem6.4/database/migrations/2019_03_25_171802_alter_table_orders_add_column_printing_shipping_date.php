<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersAddColumnPrintingShippingDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('printing_shipping_date')->nullable()->comment('帳票用出荷日')->after('shipping_date');
        });

        DB::statement('UPDATE orders SET printing_shipping_date = shipping_date');

        Schema::table('orders', function (Blueprint $table) {
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('printing_shipping_date');
        });
    }
}
