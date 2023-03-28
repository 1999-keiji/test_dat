<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\StockStatus;

class AlterTableStocksModifyColumnStockStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE stocks MODIFY COLUMN stock_status TINYINT UNSIGNED NOT NULL DEFAULT %d COMMENT '状態'";
        DB::statement(sprintf($sql, StockStatus::NORMAL));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('stock_status', 1)->default(StockStatus::NORMAL)->nullable()->comment('状態')->change();
        });
    }
}
