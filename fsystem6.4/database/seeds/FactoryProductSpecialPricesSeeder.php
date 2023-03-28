<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryProductSpecialPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_product_special_prices')->insert([
            [
                'delivery_destination_code' => '1200400J01',
                'factory_code' => '0001-ODT',
                'factory_product_sequence_number' => 1,
                'currency_code' => 'JPY',
                'application_started_on' => '2017-12-06',
                'application_ended_on' => '2018-03-31',
                'unit_price' => 100.00,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'delivery_destination_code' => '1200400J01',
                'factory_code' => '0001-ODT',
                'factory_product_sequence_number' => 1,
                'currency_code' => 'JPY',
                'application_started_on' => '2018-04-01',
                'application_ended_on' => '2019-04-01',
                'unit_price' => 108.00,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'delivery_destination_code' => '1200400J01',
                'factory_code' => '0001-ODT',
                'factory_product_sequence_number' => 2,
                'currency_code' => 'JPY',
                'application_started_on' => '2018-04-01',
                'application_ended_on' => '2019-04-01',
                'unit_price' => 90.00,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
