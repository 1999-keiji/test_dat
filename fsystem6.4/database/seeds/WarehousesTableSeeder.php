<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class WarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouses')->insert([
            [
                'warehouse_code'         => 'ODT-0001',
                'warehouse_name'         => '大館第一倉庫',
                'warehouse_abbreviation' => '大館第一',
                'country_code'           => 'JP',
                'postal_code'            => '017-0021',
                'prefecture_code'        => '05',
                'address'                => '秋田県大館市',
                'address2'               => '釈迦内上堰上堰',
                'address3'               => '86',
                'abroad_address'         => '',
                'abroad_address2'        => '',
                'abroad_address3'        => '',
                'phone_number'           => '0186-59-0000',
                'extension_number'       => '8811',
                'fax_number'             => '0186-59-0001',
                'mail_address'           => 'odate@vvf.xx.xx',
                'can_display'            => true,
                'remark'                 => '',
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'warehouse_code'         => 'ODT-0002',
                'warehouse_name'         => '大館第二倉庫',
                'warehouse_abbreviation' => '大館第二',
                'country_code'           => 'JP',
                'postal_code'            => '017-0021',
                'prefecture_code'        => '05',
                'address'                => '秋田県大館市',
                'address2'               => '釈迦内上堰上堰',
                'address3'               => '86',
                'abroad_address'         => '',
                'abroad_address2'        => '',
                'abroad_address3'        => '',
                'phone_number'           => '0186-59-0000',
                'extension_number'       => '8811',
                'fax_number'             => '0186-59-0001',
                'mail_address'           => 'odate@vvf.xx.xx',
                'can_display'            => true,
                'remark'                 => '',
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'warehouse_code'         => 'ODT-0003',
                'warehouse_name'         => '大館第三倉庫',
                'warehouse_abbreviation' => '大館第三',
                'country_code'           => 'JP',
                'postal_code'            => '017-0021',
                'prefecture_code'        => '05',
                'address'                => '秋田県大館市',
                'address2'               => '釈迦内上堰上堰',
                'address3'               => '86',
                'abroad_address'         => '',
                'abroad_address2'        => '',
                'abroad_address3'        => '',
                'phone_number'           => '0186-59-0000',
                'extension_number'       => '8811',
                'fax_number'             => '0186-59-0001',
                'mail_address'           => 'odate@vvf.xx.xx',
                'can_display'            => true,
                'remark'                 => '',
                'created_by'             => 'BATCH',
                'created_at'             => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by'             => 'BATCH',
                'updated_at'             => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
