<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoriesAddColumnNeedsToSlidePrintingShippingDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->boolean('needs_to_slide_printing_shipping_date')
                ->default(0)
                ->comment('帳票用出荷日調整フラグ')
                ->after('collection_guide_message2');
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
            $table->dropColumn('needs_to_slide_printing_shipping_date');
        });
    }
}
