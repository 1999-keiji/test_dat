<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;

class DeliveryDestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_destinations')->insert([
            [
                'delivery_destination_code' => 'HANE000037',
                'delivery_destination_name' => '株式会社阪栄フーズ',
                'delivery_destination_abbreviation' => '阪栄フーズ',
                'delivery_destination_name_kana' => 'ﾊﾝｴｲﾌｰｽﾞ',
                'country_code' => 'JP',
                'postal_code' => '810-0071',
                'prefecture_code' => '40',
                'address' => '福岡県福岡市中央区那の津3-9-8',
                'phone_number' => '092-401-1530',
                'delivery_destination_class' => 1,
                'remark' => '',
                'base_plus_delete_flag' => 0,
                'base_plus_user_created_by' => '07862',
                'base_plus_program_created_by' => 'BSCB180F',
                'base_plus_created_at' => '2017-12-06 10:48:43',
                'base_plus_user_updated_by' => '07862',
                'base_plus_program_updated_by' => 'BSCB180F',
                'base_plus_updated_at' => '2017-12-12 18:28:36',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'delivery_destination_code' => '1200400J01',
                'delivery_destination_name' => '株式会社サトー商会',
                'delivery_destination_abbreviation' => 'サトー商会',
                'delivery_destination_name_kana' => 'ｻﾄｰ',
                'country_code' => 'JP',
                'postal_code' => '983-8856',
                'prefecture_code' => '05',
                'address' => '宮城県仙台市宮城野区扇町5-6-22',
                'phone_number' => '022-236-5600',
                'delivery_destination_class' => 1,
                'remark' => '',
                'base_plus_delete_flag' => 0,
                'base_plus_user_created_by' => '07862',
                'base_plus_program_created_by' => 'BSCB180F',
                'base_plus_created_at' => '2017-12-06 10:48:43',
                'base_plus_user_updated_by' => '07862',
                'base_plus_program_updated_by' => 'BSCB180F',
                'base_plus_updated_at' => '2017-12-12 18:28:36',
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
