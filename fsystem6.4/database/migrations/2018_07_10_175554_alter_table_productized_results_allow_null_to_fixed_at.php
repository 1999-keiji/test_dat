<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultsAllowNullToFixedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_results', function (Blueprint $table) {
            $table->datetime('fixed_at')->nullable()->comment('確定日時')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productized_results', function (Blueprint $table) {
            $table->datetime('fixed_at')->comment('確定日時')->change();
        });
    }
}
