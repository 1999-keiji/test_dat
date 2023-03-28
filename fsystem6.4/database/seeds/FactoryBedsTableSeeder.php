<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryBedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_beds')->insert([
            [
                'factory_code' => '0001-ODT',
                'row' => '1',
                'column' => '1',
                'floor' => '1',
                'x_coordinate_panel' => '20',
                'y_coordinate_panel' => '20',
                'irradiation' => 'あり',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'row' => '2',
                'column' => '1',
                'floor' => '1',
                'x_coordinate_panel' => '20',
                'y_coordinate_panel' => '20',
                'irradiation' => 'あり',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'row' => '3',
                'column' => '1',
                'floor' => '2',
                'x_coordinate_panel' => '40',
                'y_coordinate_panel' => '40',
                'irradiation' => 'あり',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
