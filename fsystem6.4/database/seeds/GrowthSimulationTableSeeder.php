<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class GrowthSimulationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('growth_simulation')->insert([
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'simulation_name' => 'フリルレタス10月出荷分',
                'detail_number' => '1',
                'fixed_by' => '',
                'fixed_start_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'fixed_comp_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'work_by' => '',
                'work_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'simulation_preparation_start_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'simulation_preparation_comp_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '2',
                'factory_species_code' => '0001-FL-ODT',
                'simulation_name' => 'フリルレタス11月出荷分',
                'detail_number' => '1',
                'fixed_by' => null,
                'fixed_start_at' => null,
                'fixed_comp_at' => null,
                'work_by' => null,
                'work_at' => null,
                'simulation_preparation_start_at' => null,
                'simulation_preparation_comp_at' => null,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
