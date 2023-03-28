<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class CorporationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corporations')->insert([
            [
                'corporation_code' => '300100',
                'corporation_name' => '株式会社バイテックファーム大館',
                'corporation_abbreviation' => 'VF大館',
                'country_code' => 'JP',
                'postal_code' => '017-0012',
                'prefecture_code' => '05',
                'address' => '秋田県大館市釈迦内上堰上堰86',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'corporation_code' => '300200',
                'corporation_name' => '株式会社バイテックファーム七尾',
                'corporation_abbreviation' => 'VF七尾',
                'country_code' => 'JP',
                'postal_code' => '929-2126',
                'prefecture_code' => '17',
                'address' => '石川県七尾市大津町18-28-5',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
