<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersHistoriesChangeRegistrationDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_histories', function (Blueprint $table) {
            $table->datetime('registration_date')->comment('登録日時')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders_histories', function (Blueprint $table) {
            $table->date('registration_date')->comment('登録日')->change();
        });
    }
}
