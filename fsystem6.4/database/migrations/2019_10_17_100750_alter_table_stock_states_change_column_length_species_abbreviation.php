<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockStatesChangeColumnLengthSpeciesAbbreviation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->string('species_abbreviation', 20)->nullable()->comment('品種略称')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_states', function (Blueprint $table) {
            $table->string('species_abbreviation', 10)->nullable()->comment('品種略称')->change();
        });
    }
}
