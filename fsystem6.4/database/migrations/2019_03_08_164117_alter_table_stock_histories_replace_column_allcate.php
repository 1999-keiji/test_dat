<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStockHistoriesReplaceColumnAllcate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropColumn('allcate');
            $table->boolean('allocation_flag')->default(false)->comment('引当')->after('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropColumn('allocation_flag');
            $table->string('allcate',1)->default(1)->comment('引当')->after('delivery_date');
        });
    }
}
