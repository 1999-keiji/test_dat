<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoryProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factory_products')->insert([
            [
                'factory_code'                  => '0001-ODT',
                'sequence_number'               => 1,
                'product_code'                  => 'OLT0001',
                'factory_product_name'          => 'ﾌﾘﾙﾚﾀｽ_1',
                'factory_product_abbreviation'  => 'ﾌﾘﾙ_1',
                'number_of_heads'               => 0,
                'weight_per_number_of_heads'    => 0,
                'input_group'                   => '',
                'number_of_cases'               => 10,
                'unit'                          => '',
                'remark'                        => '',
                'created_by'                    => 'BATCH',
                'created_at'                    => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'                    => 'BATCH',
                'updated_at'                    => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'sequence_number'               => 2,
                'product_code'                  => 'OLT0001',
                'factory_product_name'          => 'ﾌﾘﾙﾚﾀｽ_2',
                'factory_product_abbreviation'  => 'ﾌﾘﾙ_2',
                'number_of_heads'               => 0,
                'weight_per_number_of_heads'    => 0,
                'input_group'                   => '',
                'number_of_cases'               => 10,
                'unit'                          => '',
                'remark'                        => '',
                'created_by'                    => 'BATCH',
                'created_at'                    => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'                    => 'BATCH',
                'updated_at'                    => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0001-ODT',
                'sequence_number'               => 3,
                'product_code'                  => 'OLT0001',
                'factory_product_name'          => 'ﾌﾘﾙﾚﾀｽ_3',
                'factory_product_abbreviation'  => 'ﾌﾘﾙ_3',
                'number_of_heads'               => 0,
                'weight_per_number_of_heads'    => 0,
                'input_group'                   => '',
                'number_of_cases'               => 10,
                'unit'                          => '',
                'remark'                        => '',
                'created_by'                    => 'BATCH',
                'created_at'                    => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'                    => 'BATCH',
                'updated_at'                    => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'factory_code'                  => '0002-NNO',
                'sequence_number'               => 1,
                'product_code'                  => 'OLT0049',
                'factory_product_name'          => 'ｸﾞﾘｰﾝﾘｰﾌ_1',
                'factory_product_abbreviation'  => 'ﾘｰﾌ_1',
                'number_of_heads'               => 0,
                'weight_per_number_of_heads'    => 0,
                'input_group'                   => '',
                'number_of_cases'               => 10,
                'unit'                          => '',
                'remark'                        => '',
                'created_by'                    => 'BATCH',
                'created_at'                    => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'                    => 'BATCH',
                'updated_at'                    => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
