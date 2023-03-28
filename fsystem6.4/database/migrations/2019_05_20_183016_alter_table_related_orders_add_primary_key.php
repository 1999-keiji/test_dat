<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRelatedOrdersAddPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('related_orders', function (Blueprint $table) {
            $table->primary(['temporary_order_number', 'fixed_order_number'], 'related_orders_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('related_orders', function (Blueprint $table) {
            $table->dropPrimary('related_orders_primary');
        });
    }
}
