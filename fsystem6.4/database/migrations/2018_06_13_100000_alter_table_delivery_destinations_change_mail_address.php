<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDeliveryDestinationsChangeMailAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->string('mail_address', 250)->comment('メールアドレス')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->string('mail_address', 30)->comment('メールアドレス')->change();
        });
    }
}
