<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrdersChangeTypeOrderQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY order_quantity INT COMMENT '注文数'");
        DB::statement("ALTER TABLE orders MODIFY order_amount DECIMAL(16, 3) COMMENT '合価'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY order_quantity INT UNSIGNED COMMENT '注文数'");
        DB::statement("ALTER TABLE orders MODIFY order_amount DECIMAL(16, 3) UNSIGNED COMMENT '合価'");
    }
}
