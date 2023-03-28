<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactorySpeciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_species')->insert([
            [
                'factory_code' => '0001-ODT',
                'factory_species_code' => '0001-FL-ODT',
                'species_code' => '0001-FL',
                'factory_species_name' => 'フリルレタス',
                'weight' => '40',
                'remark' => '',
                'can_select_on_simulation' => true,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
