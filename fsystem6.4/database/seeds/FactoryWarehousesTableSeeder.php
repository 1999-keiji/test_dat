<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryWarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_warehouses')->insert([
            [
                'factory_code'           => '0001-ODT',
                'warehouse_code'         => 'ODT-0001',
                'priority'               => 1,
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'           => '0001-ODT',
                'warehouse_code'         => 'ODT-0002',
                'priority'               => 2,
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'           => '0001-ODT',
                'warehouse_code'         => 'ODT-0003',
                'priority'               => 3,
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
