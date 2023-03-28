<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Master\Factory;
use App\Models\Master\FactoryWorkingDay;
use App\ValueObjects\Date\WorkingDate;

class CreateTableFactoryWorkingDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_working_days', function (Blueprint $table) {
            $table->string('factory_code', 15)->comment('工場コード');
            $table->unsignedTinyInteger('day_of_the_week')->comment('曜日');
            $table->string('created_by', 15)->default('')->comment('作成者');
            $table->datetime('created_at')->comment('作成日時');
            $table->string('updated_by', 15)->default('')->comment('更新者');
            $table->datetime('updated_at')->comment('更新日時');

            $table->primary(['factory_code', 'day_of_the_week'], 'factory_working_days_primary');
            $table->foreign('factory_code')->references('factory_code')->on('factories');
        });

        Factory::all()->each(function ($f) {
            $factory_working_days = [
                [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::MONDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ],
                [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::TUESDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ],
                [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::WEDNESDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ],
                [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::THURSDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ],
                [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::FRIDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ]
            ];

            if ($f->work_on_saturday) {
                $factory_working_days[] = [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::SATURDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ];
            }
            if ($f->work_on_sunday) {
                $factory_working_days[] = [
                    'factory_code' => $f->factory_code,
                    'day_of_the_week' => WorkingDate::SUNDAY % WorkingDate::DAYS_PER_WEEK,
                    'created_at' => WorkingDate::now(),
                    'created_by' => 'BATCH',
                    'updated_at' => WorkingDate::now(),
                    'updated_by' => 'BATCH'
                ];
            }

            FactoryWorkingDay::insert($factory_working_days);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('factory_working_days');
    }
}
