<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCarryOverStocksAddPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carry_over_stocks', function (Blueprint $table) {
            $table->dropPrimary('carry_over_stocks_primary_keys');
            $table->primary([
                'date',
                'factory_code',
                'species_code',
                'number_of_heads',
                'weight_per_number_of_heads',
                'input_group',
            ], 'carry_over_stocks_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carry_over_stocks', function (Blueprint $table) {
            $table->dropPrimary('carry_over_stocks_primary_keys');
            $table->primary(['date', 'factory_code'], 'carry_over_stocks_primary_keys');
        });
    }
}
