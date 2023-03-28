<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStocksChangeTypeOfMovingDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->date('moving_start_at')->nullable()->comment('移動開始日')->after('before_warehouse_code');
            $table->date('moving_complete_at')->nullable()->comment('移動完了日')->after('moving_start_at');
        });

        DB::statement('UPDATE stocks SET moving_start_at = movig_start_at');
        DB::statement('UPDATE stocks SET moving_complete_at = movig_comprate_at');

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('movig_start_at');
            $table->dropColumn('movig_comprate_at');
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
            $table->datetime('movig_start_at')->nullable()->comment('移動完了日')->after('before_warehouse_code');
            $table->datetime('movig_comprate_at')->nullable()->comment('移動完了日')->after('movig_start_at');
        });

        DB::statement('UPDATE stocks SET movig_start_at = moving_start_at');
        DB::statement('UPDATE stocks SET movig_comprate_at = moving_complete_at');

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('moving_start_at');
            $table->dropColumn('moving_complete_at');
        });
    }
}
