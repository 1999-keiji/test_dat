<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFactoryProductsChangeNumberOfHeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_products', function (Blueprint $table) {
            $table->unsignedDecimal('number_of_heads', 6, 1)->default(0.0)->comment('基本入り株数')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factory_products', function (Blueprint $table) {
            $table->unsignedSmallInteger('number_of_heads')->default(0)->comment('基本入り株数')->change();
        });
    }
}
