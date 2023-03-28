<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\BasisForRecordingSales;
use App\ValueObjects\Enum\ClosingDate;
use App\ValueObjects\Enum\PaymentTimingDate;
use App\ValueObjects\Enum\PaymentTimingMonth;
use App\ValueObjects\Enum\RoundingType;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('customer_code', 15)->comment('得意先コード');
            $table->boolean('is_default_customer')->default(false)->comment('デフォルト得意先');
            $table->string('customer_name', 50)->default('')->comment('得意先名称1');
            $table->string('customer_name2', 50)->default('')->comment('得意先名称2');
            $table->string('customer_abbreviation', 20)->default('')->comment('得意先略称');
            $table->string('customer_name_kana', 30)->default('')->comment('得意先カナ名称');
            $table->string('customer_name_english', 65)->default('')->comment('得意先英字名称');
            $table->string('country_code', 2)->default('')->comment('国コード');
            $table->string('postal_code', 10)->default('')->comment('郵便番号');
            $table->string('prefecture_code', 3)->default('')->comment('都道府県コード');
            $table->string('address', 50)->default('')->comment('住所1');
            $table->string('address2', 50)->default('')->comment('住所2');
            $table->string('address3', 50)->default('')->comment('住所3');
            $table->string('abroad_address', 50)->default('')->comment('海外住所1');
            $table->string('abroad_address2', 50)->default('')->comment('海外住所2');
            $table->string('abroad_address3', 50)->default('')->comment('海外住所3');
            $table->string('phone_number', 20)->default('')->comment('電話番号');
            $table->string('extension_number', 15)->default('')->comment('内線番号');
            $table->string('fax_number', 15)->default('')->comment('FAX番号');
            $table->string('mail_address', 30)->default('')->comment('メールアドレス');
            $table->unsignedTinyInteger('closing_date')->default(ClosingDate::END_OF_MONTH)->comment('請求締日');
            $table->unsignedTinyInteger('payment_timing_month')->default(PaymentTimingMonth::NEXT_MONTH)->comment('入金サイト(月)');
            $table->unsignedTinyInteger('payment_timing_date')->default(PaymentTimingDate::END_OF_MONTH)->comment('入金サイト(日)');
            $table->unsignedTinyInteger('basis_for_recording_sales')->default(BasisForRecordingSales::DELIVERY)->comment('売上計上基準');
            $table->unsignedTinyInteger('rounding_type')->default(RoundingType::FLOOR)->comment('端数処理');
            $table->boolean('can_display')->default(true)->comment('表示区分');
            $table->string('remark', 255)->default('')->comment('備考');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary('customer_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
