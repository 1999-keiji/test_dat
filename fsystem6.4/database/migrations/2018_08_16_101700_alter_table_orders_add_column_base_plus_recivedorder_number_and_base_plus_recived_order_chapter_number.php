<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersAddColumnBasePlusRecivedOrderNumberAndBasePlusRecivedOrderChapterNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('base_plus_recived_order_number', 10)->nullable()->comment('Base+受注番号')->after('delivery_destination_code');
            $table->string('base_plus_recived_order_chapter_number', 3)->nullable()->comment('Base+受注項番')->after('base_plus_recived_order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('base_plus_recived_order_number');
            $table->dropColumn('base_plus_recived_order_chapter_number');
        });
    }
}
