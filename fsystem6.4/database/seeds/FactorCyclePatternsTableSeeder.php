<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactorCyclePatternsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_cycle_patterns')->insert([
            [
                'factory_code' => '0001-ODT',
                'sequence_number' => '1',
                'cycle_pattern_name' => 'A',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code' => '0001-ODT',
                'sequence_number' => '2',
                'cycle_pattern_name' => 'B',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
