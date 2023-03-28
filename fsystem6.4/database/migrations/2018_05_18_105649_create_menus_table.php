<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->string('category', 100)->comment('カテゴリ');
            $table->unsignedInteger('tab_no')->default(0)->comment('タブ番号');
            $table->unsignedInteger('group_row_no')->default(0)->comment('グループ列番号');
            $table->unsignedTinyInteger('group_column_no')->default(0)->comment('グループ行番号');
            $table->unsignedInteger('category_order')->default(0)->comment('カテゴリ表示順');
            $table->string('tab_name', 100)->comment('タブ名');
            $table->string('group_name', 100)->comment('グループ名');
            $table->string('category_name', 100)->comment('カテゴリー名');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
