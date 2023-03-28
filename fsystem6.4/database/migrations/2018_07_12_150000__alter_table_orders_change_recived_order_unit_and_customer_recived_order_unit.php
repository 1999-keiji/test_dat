<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersChangeRecivedOrderUnitAndCustomerRecivedOrderUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedDecimal('recived_order_unit', 14, 5)->default(0.00000)->comment('受注単価')->change();
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->default(0.00000)->comment('得意先受注合価')->change();
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
            $table->unsignedDecimal('recived_order_unit', 14, 5)->comment('受注単価')->change();
            $table->unsignedDecimal('customer_recived_order_unit', 14, 5)->comment('得意先受注合価')->change();
        });
    }
}
