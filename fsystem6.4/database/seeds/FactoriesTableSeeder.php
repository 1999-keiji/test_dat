<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class FactoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('factories')->insert([
            [
                'factory_code' => '0001-ODT',
                'factory_name' => 'バイテックファーム大館 植物工場',
                'factory_abbreviation' => '大館工場',
                'country_code' => 'JP',
                'postal_code' => '017-0012',
                'prefecture_code' => '05',
                'address' => '秋田県大館市釈迦内上堰上堰86',
                'corporation_code' => '300100',
                'invoice_corporation_name' => '株式会社 バイテックファーム大館',
                'invoice_postal_code' => '017-0012',
                'invoice_address' => '秋田県大館市釈迦内上堰上堰86',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'bank_name' => 'testぎんこう',
                'bank_branch_name' => 'テスト銀行支店',
                'bank_account_number' => '000000',
                'bank_account_holder' => 'テスト',
                'symbolic_code' =>'000',
                'collection_staff_name' => 'test',
                'invoice_bank_name' => 'test',
                'invoice_bank_branch_name' =>'test',
                'invoice_bank_account_number' =>'test',
                'invoice_bank_account_holder' => 'test'
            ],
            [
                'factory_code' => '0002-NNO',
                'factory_name' => 'バイテックファーム七尾 植物工場',
                'factory_abbreviation' => '七尾工場',
                'country_code' => 'JP',
                'postal_code' => '929-2126',
                'prefecture_code' => '17',
                'address' => '石川県七尾市大津町18-28-5',
                'corporation_code' => '300200',
                'invoice_corporation_name' => '株式会社 バイテックファーム七尾',
                'invoice_postal_code' => '929-2126',
                'invoice_address' => '石川県七尾市大津町18-28-5',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'bank_name' => 'testぎんこう',
                'bank_branch_name' => 'テスト銀行支店',
                'bank_account_number' => '000000',
                'bank_account_holder' => 'テスト',
                'symbolic_code' =>'002',
                'collection_staff_name' => 'test',
                'invoice_bank_name' => 'test',
                'invoice_bank_branch_name' =>'test',
                'invoice_bank_account_number' =>'test',
                'invoice_bank_account_holder' => 'test'
            ]
        ]);
    }
}
