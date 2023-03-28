<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableReturnedProductsChangeTypeReturnedOn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returned_products', function (Blueprint $table) {
            $table->date('returned_on')->comment('返品日')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returned_products', function (Blueprint $table) {
            $table->datetime('returned_on')->comment('返品日')->change();
        });
    }
}
