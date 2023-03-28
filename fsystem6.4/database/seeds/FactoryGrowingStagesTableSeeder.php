<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryGrowingStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_growing_stages')->insert([
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'sequence_number' => '1',
                'growing_stage' => '1',
                'growing_stage_name' => 'ステージ1',
                'label_color' => '73743b',
                'growing_term' => '1',
                'number_of_holes' => '20',
                'yield_rate' => '0.00',
                'cycle_pattern_sequence_number' => '1',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'sequence_number' => '2',
                'growing_stage' => '2',
                'growing_stage_name' => 'ステージ2',
                'label_color' => 'ffccff',
                'growing_term' => '1',
                'number_of_holes' => '20',
                'yield_rate' => '0.00',
                'cycle_pattern_sequence_number' => '1',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'sequence_number' => '3',
                'growing_stage' => '2',
                'growing_stage_name' => 'ステージ3',
                'label_color' => 'ffc000',
                'growing_term' => '1',
                'number_of_holes' => '20',
                'yield_rate' => '0.00',
                'cycle_pattern_sequence_number' => '2',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'sequence_number' => '4',
                'growing_stage' => '3',
                'growing_stage_name' => 'ステージ4',
                'label_color' => '99ff99',
                'growing_term' => '1',
                'number_of_holes' => '20',
                'yield_rate' => '0.00',
                'cycle_pattern_sequence_number' => '2',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'sequence_number' => '5',
                'growing_stage' => '4',
                'growing_stage_name' => 'ステージ5',
                'label_color' => '9999FF',
                'growing_term' => '1',
                'number_of_holes' => '20',
                'yield_rate' => '0.00',
                'cycle_pattern_sequence_number' => '1',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
