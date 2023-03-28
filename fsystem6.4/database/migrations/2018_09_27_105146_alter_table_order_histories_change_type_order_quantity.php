<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderHistoriesChangeTypeOrderQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE order_histories MODIFY order_quantity INT COMMENT '注文数'");
        DB::statement("ALTER TABLE order_histories MODIFY order_amount DECIMAL(16, 3) COMMENT '合価'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE order_histories MODIFY order_quantity INT UNSIGNED COMMENT '注文数'");
        DB::statement("ALTER TABLE order_histories MODIFY order_amount DECIMAL(16, 3) UNSIGNED COMMENT '合価'");
    }
}
