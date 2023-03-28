<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;
use App\ValueObjects\Enum\StatementOfShipmentOutputClass;

class AlterTableDeliveryDestinationsAddColumnsEndUserCodAndOthers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->string('collection_request_remark', 50)->default('')->comment('集荷案内書コメント')->after('collection_time_sequence_number');
            $table->string('end_user_code', 8)->default('')->comment('エンドユーザコード')->after('collection_request_remark');
            $table->unsignedTinyInteger('fsystem_statement_of_delivery_output_class')->default(FsystemStatementOfDeliveryOutputClass::DISPLAY_PRICE)->comment('Fシステム納品書出力区分')->after('end_user_code');
            $table->unsignedTinyInteger('statement_of_shipment_output_class')->default(StatementOfShipmentOutputClass::ENABLED)->comment('出荷案内書出力区分')->after('fsystem_statement_of_delivery_output_class');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_destinations', function (Blueprint $table) {
            $table->dropColumn('collection_request_remark');
            $table->dropColumn('end_user_code');
            $table->dropColumn('fsystem_statement_of_delivery_output_class');
            $table->dropColumn('statement_of_shipment_output_class');
        });
    }
}
