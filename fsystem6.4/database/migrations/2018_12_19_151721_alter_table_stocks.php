<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->date('harvesting_date')->comment('収穫日')->change();
            $table->unsignedSmallInteger('disposal_weight')->default(0)->comment('廃棄重量')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->datetime('harvesting_date')->comment('収穫日')->change();
            $table->smallInteger('disposal_weight')->default(0)->comment('廃棄重量')->change();
        });
    }
}
