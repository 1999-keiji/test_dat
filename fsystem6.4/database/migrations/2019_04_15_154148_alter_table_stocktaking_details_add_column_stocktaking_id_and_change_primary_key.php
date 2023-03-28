<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStocktakingDetailsAddColumnStocktakingIdAndChangePrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE stocktaking_details DROP PRIMARY KEY');

        Schema::table('stocktaking_details', function (Blueprint $table) {
            $table->increments('stocktaking_id')->comment('棚卸ID')->first();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE stocktaking_details DROP PRIMARY KEY');

        Schema::table('stocktaking_details', function (Blueprint $table) {
            $table->dropColumn('stocktaking_id');
            $table->primary([
                'factory_code',
                'warehouse_code',
                'stocktaking_month',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
                'number_of_cases'
            ], 'stocktaking_details_primary_keys');
        });
    }
}