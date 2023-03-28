<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesChangeTypeOffixedShippingSharingFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->boolean('fixed_shipping_sharing_flag')->default(false)->comment('出荷実績連携フラグ')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            $table->string('fixed_shipping_sharing_flag', 1)->default(false)->comment('出荷実績連携フラグ')->change();
        });
    }
}
