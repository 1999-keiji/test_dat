<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableFactoryProductsAddColumnCanBeTransportedDouble extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factory_products', function (Blueprint $table) {
            $table->boolean('can_be_transported_double')->default(false)->comment('ニコイチ配送可否')->after('unit');
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
            $table->dropColumn('can_be_transported_double');
        });
    }
}
