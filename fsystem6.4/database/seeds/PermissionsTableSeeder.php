<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cake\Chronos\Chronos;
use App\ValueObjects\Enum\Affiliation;
use App\ValueObjects\Enum\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        DB::table('permissions')->insert([
            // シミュレーション一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'growth_simulation',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'growth_simulation',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'growth_simulation',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'growth_simulation',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'growth_simulation',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // シミュレーション確定一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'growth_simulation_fixed',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'growth_simulation_fixed',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'growth_simulation_fixed',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'growth_simulation_fixed',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'growth_simulation_fixed',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // ベッド状況確認
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'bed_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'bed_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'bed_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'bed_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'bed_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 生販管理表サマリー
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'growth_sale_management_summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'growth_sale_management_summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'growth_sale_management_summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'growth_sale_management_summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'growth_sale_management_summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 生販管理表
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'growth_sale_management',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'growth_sale_management',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'growth_sale_management',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'growth_sale_management',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'growth_sale_management',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 生産計画管理表
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'growth_planned_table',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'growth_planned_table',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'growth_planned_table',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'growth_planned_table',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'growth_planned_table',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 施設利用状況一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'facility_status_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'facility_status_list',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'facility_status_list',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'facility_status_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'facility_status_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 受注フォーキャスト
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'order_forecasts',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'order_forecasts',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'order_forecasts',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'order_forecasts',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'order_forecasts',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 注文一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'order_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'order_list',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'order_list',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'order_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'order_list',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // ホワイトボード参照
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'whiteboard_reference',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'whiteboard_reference',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'whiteboard_reference',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'whiteboard_reference',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'whiteboard_reference',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 注文入力
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'order_input',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'order_input',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'order_input',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'order_input',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'order_input',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 注文Excel取込
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'purchase_order_excel_import',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'purchase_order_excel_import',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'purchase_order_excel_import',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'purchase_order_excel_import',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'purchase_order_excel_import',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 返品入力
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'returned_products',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'returned_products',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'returned_products',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'returned_products',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'returned_products',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 作業指示書
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'work_instruction',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'work_instruction',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'work_instruction',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'work_instruction',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'work_instruction',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 活動実績一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'activity_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'activity_results',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'activity_results',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'activity_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'activity_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 製品化実績入力
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'productized_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'productized_results',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'productized_results',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'productized_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'productized_results',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 出荷作業帳票出力
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'form_output',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'form_output',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'form_output',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'form_output',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'form_output',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 集荷依頼書
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'collection_request',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'collection_request',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'collection_request',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'collection_request',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'collection_request',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 請求書締め
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'invoices',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'invoices',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'invoices',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'invoices',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'invoices',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 出荷確定
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'shipment_fix',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'shipment_fix',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'shipment_fix',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'shipment_fix',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'shipment_fix',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 出荷データ出力
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'shipment_data_export',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'shipment_data_export',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'shipment_data_export',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'shipment_data_export',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'shipment_data_export',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 在庫サマリー
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stocks.summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stocks.summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stocks.summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stocks.summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stocks.summary',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 在庫
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stocks',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stocks',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stocks',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stocks',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stocks',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 廃棄登録
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stocks.dispose',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stocks.dispose',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stocks.dispose',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stocks.dispose',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stocks.dispose',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 在庫棚卸
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stocktaking',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stocktaking',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stocktaking',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stocktaking',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stocktaking',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 在庫状況確認
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stock_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stock_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stock_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stock_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stock_states',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 在庫履歴一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'stock_histories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'stock_histories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'stock_histories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'stock_histories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'stock_histories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 商品マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'products',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'products',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'products',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'products',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'products',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 品種マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'species',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'species',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'species',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'species',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'species',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 法人マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'corporations',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'corporations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'corporations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'corporations',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'corporations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 工場マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'factories',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'factories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'factories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'factories',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'factories',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 得意先マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'customers',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'customers',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'customers',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'customers',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'customers',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // エンドユーザマスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'end_users',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'end_users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'end_users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'end_users',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'end_users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 納入先マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'delivery_destinations',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'delivery_destinations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'delivery_destinations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'delivery_destinations',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'delivery_destinations',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 倉庫マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'warehouses',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'warehouses',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'warehouses',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'warehouses',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'warehouses',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // リードタイム一覧
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'lead_time',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'lead_time',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'lead_time',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'lead_time',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'lead_time',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // 運送会社マスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'transport_companies',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'transport_companies',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'transport_companies',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'transport_companies',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'transport_companies',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // ユーザマスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'users',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'users',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'users',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            // カレンダーマスタ
            [
                'affiliation' => Affiliation::VVF,
                'category' => 'calendars',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_OTHER,
                'category' => 'calendars',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::VVF_SALE,
                'category' => 'calendars',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY,
                'category' => 'calendars',
                'permission' => Permission::WRITABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
            [
                'affiliation' => Affiliation::FACTORY_OTHER,
                'category' => 'calendars',
                'permission' => Permission::READABLE,
                'created_by' => 'BATCH',
                'created_at' => Chronos::now()->format('Y-m-d H:i:s'),
                'updated_by' => 'BATCH',
                'updated_at' => Chronos::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
