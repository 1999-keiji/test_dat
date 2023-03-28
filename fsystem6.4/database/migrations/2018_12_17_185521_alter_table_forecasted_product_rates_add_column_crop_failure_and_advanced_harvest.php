<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableForecastedProductRatesAddColumnCropFailureAndAdvancedHarvest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forecasted_product_rates', function (Blueprint $table) {
            $table->integer('crop_failure')->default(0)->comment('収穫廃棄')->after('product_rate');
            $table->integer('advanced_harvest')->default(0)->comment('前採り')->after('crop_failure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forecasted_product_rates', function (Blueprint $table) {
            $table->dropColumn(['crop_failure', 'advanced_harvest']);
        });
    }
}
