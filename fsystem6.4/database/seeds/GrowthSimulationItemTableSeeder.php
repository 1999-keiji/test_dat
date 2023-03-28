<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class GrowthSimulationItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('growth_simulation_item')->insert([
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'detail_id' => '1',
                'growing_stages_sequence_number' => '1',
                'input_change' => '1',
                'growing_stage' => '1',
                'date' => Chronos::now()->format('Y-m-d'),
                'bed_number' => 10,
                'panel_number' => '4',
                'stock_number' => '10',
                'growth_days' => '7',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'detail_id' => '1',
                'growing_stages_sequence_number' => '2',
                'input_change' => '1',
                'growing_stage' => '2',
                'date' => Chronos::now()->format('Y-m-d'),
                'bed_number' => 20,
                'panel_number' => '4',
                'stock_number' => '10',
                'growth_days' => '7',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'detail_id' => '1',
                'growing_stages_sequence_number' => '3',
                'input_change' => '1',
                'growing_stage' => '2',
                'date' => Chronos::now()->format('Y-m-d'),
                'bed_number' => 30,
                'panel_number' => '4',
                'stock_number' => '10',
                'growth_days' => '7',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'detail_id' => '1',
                'growing_stages_sequence_number' => '4',
                'input_change' => '1',
                'growing_stage' => '3',
                'date' => Chronos::now()->format('Y-m-d'),
                'bed_number' => 40,
                'panel_number' => '4',
                'stock_number' => '10',
                'growth_days' => '7',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'simulation_id' => '1',
                'factory_species_code' => '0001-FL-ODT',
                'detail_id' => '1',
                'growing_stages_sequence_number' => '5',
                'input_change' => '1',
                'growing_stage' => '4',
                'date' => Chronos::now()->format('Y-m-d'),
                'bed_number' => 40,
                'panel_number' => '4',
                'stock_number' => '10',
                'growth_days' => '7',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
