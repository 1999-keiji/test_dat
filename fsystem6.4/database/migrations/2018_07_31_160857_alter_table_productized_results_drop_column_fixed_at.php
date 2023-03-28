<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductizedResultsDropColumnFixedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productized_results', function (Blueprint $table) {
            $table->dropColumn('fixed_at');
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
            $table->datetime('fixed_at')->nullable()->comment('確定日時')->after('weight_of_discarded');
        });
    }
}
