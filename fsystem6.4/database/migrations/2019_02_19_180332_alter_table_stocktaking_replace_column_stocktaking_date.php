<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStocktakingReplaceColumnStocktakingDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocktaking', function (Blueprint $table) {
            $table->dropPrimary('stocktaking_primary_keys');
            $table->dropColumn('stocktaking_date');
            $table->string('stocktaking_month', 7)->comment('棚卸年月')->after('warehouse_code');
            $table->primary(['factory_code', 'warehouse_code', 'stocktaking_month'], 'stocktaking_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocktaking', function (Blueprint $table) {
            $table->dropPrimary('stocktaking_primary_keys');
            $table->dropColumn('stocktaking_month');
            $table->datetime('stocktaking_date')->nullable()->comment('棚卸年月')->after('warehouse_code');
            $table->primary(['factory_code', 'stocktaking_date'], 'stocktaking_primary_keys');
        });
    }
}
