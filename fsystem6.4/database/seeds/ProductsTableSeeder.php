<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;
use App\ValueObjects\Enum\CreatingType;
use App\ValueObjects\Enum\ProductClass;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'product_code' => 'OLT0001',
                'species_code' => '0001-FL',
                'creating_type' => CreatingType::BASE_PLUS_LINKED,
                'product_name' => 'ﾌﾘﾙﾚﾀｽ 70g 12入',
                'result_addup_code' => 'OLTOLTOLT',
                'result_addup_name' => 'ﾌﾘﾙﾚﾀｽ',
                'result_addup_abbreviation' => 'ﾌﾘﾙﾚﾀｽ',
                'product_large_category' => 'OLT',
                'product_middle_category' => 'OLT',
                'product_class' => ProductClass::PRODUCT,
                'sales_order_unit' => 'ｹｰｽ',
                'sales_order_unit_quantity' => 1,
                'minimum_sales_order_unit_quantity' => 1,
                'species_name' => 'ﾌﾘﾙﾚﾀｽ',
                'net_weight' => 840,
                'gross_weight' => 840,
                'country_of_origin' => 'JP',
                'remark' => '',
                'base_plus_delete_flag' => 0,
                'base_plus_user_created_by' => '90930',
                'base_plus_program_created_by' => 'BSCB520F',
                'base_plus_created_at' => '2017-06-29 16:59:10',
                'base_plus_user_updated_by' => 'BATCH',
                'base_plus_program_updated_by' => 'BBCB8001',
                'base_plus_updated_at' => '2017-11-15 16:30:13',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_code' => 'OLT0049',
                'species_code' => '0002-GL',
                'creating_type' => CreatingType::MANUAL_CREATED,
                'product_name' => 'ｸﾞﾘｰﾝﾘｰﾌ 1kg 4入',
                'result_addup_code' => 'OLTOLTOLT',
                'result_addup_name' => 'ｸﾞﾘｰﾝﾘｰﾌ',
                'result_addup_abbreviation' => 'ｸﾞﾘｰﾝﾘｰﾌ',
                'product_large_category' => 'OLF',
                'product_middle_category' => 'OLF',
                'product_class' => ProductClass::PRODUCT,
                'sales_order_unit' => 'ｹｰｽ',
                'sales_order_unit_quantity' => 1,
                'minimum_sales_order_unit_quantity' => 1,
                'species_name' => 'ﾌﾘﾙﾚﾀｽ',
                'net_weight' => 4000,
                'gross_weight' => 4000,
                'country_of_origin' => 'JP',
                'remark' => '',
                'base_plus_delete_flag' => null,
                'base_plus_user_created_by' => null,
                'base_plus_program_created_by' => null,
                'base_plus_created_at' => null,
                'base_plus_user_updated_by' => null,
                'base_plus_program_updated_by' => null,
                'base_plus_updated_at' => '2017-11-15 16:30:13',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'product_code' => 'OLT0041',
                'species_code' => '0003-BA',
                'creating_type' => CreatingType::BASE_PLUS_LINKED,
                'product_name' => 'ﾊﾞｼﾞﾙ(M) 25g 50入',
                'result_addup_code' => 'OLTOLTOLT',
                'result_addup_name' => 'ﾊﾞｼﾞﾙ',
                'result_addup_abbreviation' => 'ﾊﾞｼﾞﾙ',
                'product_large_category' => 'OBA',
                'product_middle_category' => 'OBA',
                'product_class' => ProductClass::PRODUCT,
                'sales_order_unit' => 'ｹｰｽ',
                'sales_order_unit_quantity' => 1,
                'minimum_sales_order_unit_quantity' => 1,
                'species_name' => 'ﾌﾘﾙﾚﾀｽ',
                'net_weight' => 1250,
                'gross_weight' => 1250,
                'country_of_origin' => 'JP',
                'remark' => '',
                'base_plus_delete_flag' => 0,
                'base_plus_user_created_by' => '90930',
                'base_plus_program_created_by' => 'BSCB520F',
                'base_plus_created_at' => '2017-06-29 16:59:10',
                'base_plus_user_updated_by' => 'BATCH',
                'base_plus_program_updated_by' => 'BBCB8001',
                'base_plus_updated_at' => '2017-11-15 16:30:13',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);

        DB::table('product_prices')->insert([
            'factory_code' => '0001-ODT',
            'product_code' => 'OLT0001',
            'currency_code' => 'JPY',
            'application_started_on' => '2016-06-01',
            'unit_price' => 840,
            'base_plus_delete_flag' => 0,
            'base_plus_user_created_by' => '01775',
            'base_plus_program_created_by' => 'BSCB280F',
            'base_plus_created_at' => '2016-06-02 10:08:43',
            'base_plus_user_updated_by' => '01775',
            'base_plus_program_updated_by' => 'BSCB280F',
            'base_plus_updated_at' => '2016-06-02 10:08:43'
        ]);

        DB::table('product_special_prices')->insert([
            'delivery_destination_code' => 'HANE000037',
            'factory_code' => '0001-ODT',
            'product_code' => 'OLT0001',
            'currency_code' => 'JPY',
            'application_started_on' => '2016-06-01',
            'application_ended_on' => '2099-12-31',
            'unit_price' => 740,
            'base_plus_delete_flag' => 0,
            'base_plus_user_created_by' => '01775',
            'base_plus_program_created_by' => 'BSCB280F',
            'base_plus_created_at' => '2016-06-02 10:08:43',
            'base_plus_user_updated_by' => '01775',
            'base_plus_program_updated_by' => 'BSCB280F',
            'base_plus_updated_at' => '2016-06-02 10:08:43'
        ]);
    }
}
