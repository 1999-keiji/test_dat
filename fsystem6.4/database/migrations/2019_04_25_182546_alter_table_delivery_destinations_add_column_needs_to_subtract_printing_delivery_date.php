<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeliveryDestinationsAddColumnNeedsToSubtractPrintingDeliveryDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->boolean('needs_to_subtract_printing_delivery_date')
                ->default(false)
                ->comment('集荷依頼書用納入日調整フラグ')
                ->after('statement_of_shipment_output_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->dropColumn('needs_to_subtract_printing_delivery_date');
        });
    }
}
