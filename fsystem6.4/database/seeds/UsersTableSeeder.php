<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;
use App\Models\Master\Factory;
use App\Models\Master\Permission;
use App\ValueObjects\Enum\Affiliation;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'user_code' => 'hashimoto',
                'user_name' => '橋本勝',
                'affiliation' => Affiliation::VVF,
                'mail_address' => 'hashimoto@example.com',
                'password' => bcrypt('hashimoto.01'),
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'user_code' => 'takita',
                'user_name' => '瀧田裕士',
                'affiliation' => Affiliation::VVF_SALE,
                'mail_address' => 'takita@example.com',
                'password' => bcrypt('takita.02'),
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'user_code' => 'chishima',
                'user_name' => '千島宏貴',
                'affiliation' => Affiliation::FACTORY,
                'mail_address' => 'chishima@example.com',
                'password' => bcrypt('chishima.03'),
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
        ]);

        Factory::all()->each(function ($f) {
            DB::table('user_factories')->insert([
                'user_code' => 'chishima',
                'factory_code' => $f->factory_code,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        });

        Permission::where('affiliation', Affiliation::VVF)->get()->each(function ($p) {
            DB::table('user_permissions')->insert([
                'user_code' => 'hashimoto',
                'category' => $p->category,
                'permission' => $p->permission,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        });

        Permission::where('affiliation', Affiliation::VVF_SALE)->get()->each(function ($p) {
            DB::table('user_permissions')->insert([
                'user_code' => 'takita',
                'category' => $p->category,
                'permission' => $p->permission,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        });

        Permission::where('affiliation', Affiliation::FACTORY)->get()->each(function ($p) {
            DB::table('user_permissions')->insert([
                'user_code' => 'chishima',
                'category' => $p->category,
                'permission' => $p->permission,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ]);
        });
    }
}
