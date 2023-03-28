<?php

use Illuminate\Database\Migrations\Migration;
use App\ValueObjects\Enum\FsystemStatementOfDeliveryOutputClass;

class AlterTableDeliveryDestinationsChangeDefaultValueOfFsystemStatementOfDeliveryOutputClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'ALTER TABLE delivery_destinations MODIFY COLUMN fsystem_statement_of_delivery_output_class '.
            "TINYINT UNSIGNED NOT NULL DEFAULT %d COMMENT 'Fシステム納品書出力区分'";

        DB::statement(sprintf($sql, FsystemStatementOfDeliveryOutputClass::NOT_DISPLAY_PRICE));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = 'ALTER TABLE delivery_destinations MODIFY COLUMN fsystem_statement_of_delivery_output_class '.
            "TINYINT UNSIGNED NOT NULL DEFAULT %d COMMENT 'Fシステム納品書出力区分'";

        DB::statement(sprintf($sql, FsystemStatementOfDeliveryOutputClass::DISPLAY_PRICE));
    }
}
