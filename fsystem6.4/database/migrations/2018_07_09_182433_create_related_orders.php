<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_orders', function (Blueprint $table) {
            $table->string('temporary_order_number', 14)->comment('紐付け仮注文番号');
            $table->string('fixed_order_number', 14)->comment('紐付け確定注文番号');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_orders');
    }
}
