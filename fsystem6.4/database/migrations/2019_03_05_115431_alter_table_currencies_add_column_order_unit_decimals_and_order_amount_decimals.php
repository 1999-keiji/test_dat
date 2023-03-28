<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCurrenciesAddColumnOrderUnitDecimalsAndOrderAmountDecimals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->unsignedTinyInteger('order_unit_decimals')
                ->default(5)
                ->comment('単価小数桁数')
                ->after('currency_code');
            $table->unsignedTinyInteger('order_amount_decimals')
                ->default(3)
                ->comment('合価小数桁数')
                ->after('order_unit_decimals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn(['order_unit_decimals', 'order_amount_decimals']);
        });
    }
}
