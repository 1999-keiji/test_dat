<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;
use App\Models\Master\Permission;
use App\Models\Master\User;

class UserPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_permissions = [];
        foreach (User::all() as $u) {
            $user_permissions = Permission::select('permissions.category', 'permissions.permission')
                ->leftJoin('user_permissions', function ($join) use ($u) {
                    $join->on('user_permissions.category', '=', 'permissions.category')
                        ->where('user_permissions.user_code', $u->user_code);
                })
                ->where('permissions.affiliation', $u->affiliation)
                ->whereNull('user_permissions.permission')
                ->get()
                ->reduce(function ($user_permissions, $p) use ($u) {
                    $user_permissions[] = [
                        'user_code' => $u->user_code,
                        'category' => $p->category,
                        'permission' => $p->permission,
                        'created_by' => 'BATCH',
                        'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                        'updated_by' => 'BATCH',
                        'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
                    ];

                    return $user_permissions;
                }, $user_permissions);
        }

        DB::table('user_permissions')->insert($user_permissions);
    }
}
