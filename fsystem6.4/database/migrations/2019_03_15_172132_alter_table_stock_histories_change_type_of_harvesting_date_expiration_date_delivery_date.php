<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockHistoriesChangeTypeOfHarvestingDateExpirationDateDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->date('harvesting_date')->comment('収穫日')->change();
            $table->date('expiration_date')->nullable()->comment('有効期限')->change();
            $table->date('delivery_date')->nullable()->comment('納期')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->datetime('harvesting_date')->comment('収穫日')->change();
            $table->datetime('expiration_date')->nullable()->comment('有効期限')->change();
            $table->datetime('delivery_date')->nullable()->comment('納期')->change();
        });
    }
}
