<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;

class AlterTableFactoriesDropColumnsWorkOnSaturdayAndSunday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->dropColumn(['work_on_saturday', 'work_on_sunday']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('factories', function (Blueprint $table) {
            $table->boolean('work_on_saturday')->default(false)->comment('土曜営業')->after('mail_address');
            $table->boolean('work_on_sunday')->default(false)->comment('日曜営業')->after('work_on_saturday');
        });

        Factory::all()->each(function ($f) {
            $days_of_the_week = $f->factory_working_days->pluckIsoDayOfTheWeek();
            if (in_array(Chronos::SATURDAY, $days_of_the_week, true)) {
                $f->work_on_saturday = true;
            }
            if (in_array(Chronos::SUNDAY, $days_of_the_week, true)) {
                $f->work_on_sunday = true;
            }

            $f->save();
        });
    }
}
