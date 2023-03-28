<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactorySpeciesDropColumnBasicWeightApplicationOn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_species', function (Blueprint $table) {
            $table->dropColumn('basic_weight_application_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factory_species', function (Blueprint $table) {
            $table->date('basic_weight_application_on')
                ->nullable()
                ->comment('基本重量適用日')
                ->after('factory_species_name');
        });
    }
}
