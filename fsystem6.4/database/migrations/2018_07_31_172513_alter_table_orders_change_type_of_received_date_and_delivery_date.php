<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersChangeTypeOfReceivedDateAndDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('received_date')->nullable()->comment('注文日')->change();
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
        Schema::table('orders', function (Blueprint $table) {
            $table->datetime('received_date')->nullable()->comment('注文日')->change();
            $table->datetime('delivery_date')->nullable()->comment('納期')->change();
        });
    }
}
