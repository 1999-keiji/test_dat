<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryPanelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_panels')->insert([
            [
                'factory_code' => '0001-ODT',
                'number_of_holes' => '20',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
