<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableProductAllocationsChangeColumnOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'ALTER TABLE product_allocations MODIFY warehouse_code VARCHAR(15) NOT NULL '.
            "COMMENT '倉庫コード' AFTER order_number"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            'ALTER TABLE product_allocations MODIFY warehouse_code VARCHAR(15) NOT NULL '.
            "COMMENT '倉庫コード' AFTER updated_at"
        );
    }
}
