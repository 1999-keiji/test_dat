<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesChangeTypeOrderUnit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_histories', function (Blueprint $table) {
            DB::statement("ALTER TABLE order_histories MODIFY order_quantity INT UNSIGNED COMMENT '注文数'");
            DB::statement("ALTER TABLE order_histories MODIFY order_unit DECIMAL(14, 5) COMMENT '単価'");
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
            DB::statement("ALTER TABLE order_histories MODIFY order_quantity INT COMMENT '注文数'");
            DB::statement("ALTER TABLE order_histories MODIFY order_unit DECIMAL(14, 5) UNSIGNED COMMENT '注文数'");
        });
    }
}
