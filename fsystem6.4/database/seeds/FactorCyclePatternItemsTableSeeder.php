<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactorCyclePatternItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_cycle_pattern_items')->insert([
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'A',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'B',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],

            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'C',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'D',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '1',
                'pattern'                       => 'E',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'F',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'G',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],

            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'H',
                'day_of_the_week'               => 6,
                'number_of_panels'              => 9,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'I',
                'day_of_the_week'               => 1,
                'number_of_panels'              => 4,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'I',
                'day_of_the_week'               => 2,
                'number_of_panels'              => 5,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'I',
                'day_of_the_week'               => 3,
                'number_of_panels'              => 6,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'I',
                'day_of_the_week'               => 4,
                'number_of_panels'              => 7,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'cycle_pattern_sequence_number' => '2',
                'pattern'                       => 'I',
                'day_of_the_week'               => 5,
                'number_of_panels'              => 8,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
