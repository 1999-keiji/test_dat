<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCallendarsDropRestAddEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('callendars', function (Blueprint $table) {
            $table->dropColumn('factory_is_rest');
            $table->dropColumn('shipment_is_rest');
            $table->dropColumn('delivery_is_rest');
            $table->unsignedInteger('event')->comment('行事')->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('event');
        $table->unsignedInteger('factory_is_rest')->comment('工場休')->after('date');
        $table->unsignedInteger('shipment_is_rest')->comment('出荷休')->after('factory_is_rest');
        $table->unsignedInteger('delivery_is_rest')->comment('納入休')->after('shipment_is_rest');
    }
}
