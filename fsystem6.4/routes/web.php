<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'AuthController@index')
    ->name('auth.index')->middleware(['guest']);
Route::post('login', 'AuthController@login')
    ->name('auth.login')->middleware(['guest']);
Route::post('logout', 'AuthController@logout')
    ->name('auth.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('', 'IndexController@index')
        ->name('index');
    Route::post('clear', 'IndexController@clear')
        ->name('index.clear');
    Route::get('password', 'AuthController@password')
        ->name('auth.password.index');
    Route::patch('password', 'AuthController@changePassword')
        ->name('auth.password.change');

    // マスタ管理メニュー
    // 商品マスタ
    Route::get('master/products', 'Master\ProductsController@index')
        ->name('master.products.index')->middleware(['access']);
    Route::get('master/products/add', 'Master\ProductsController@add')
        ->name('master.products.add')->middleware(['save']);
    Route::get('master/products/{product}', 'Master\ProductsController@edit')
        ->name('master.products.edit')->middleware(['access']);
    Route::post('master/products/search', 'Master\ProductsController@search')
        ->name('master.products.search')->middleware(['access']);
    Route::post('master/products', 'Master\ProductsController@create')
        ->name('master.products.create')->middleware(['save']);
    Route::patch('master/products/{product}', 'Master\ProductsController@update')
        ->name('master.products.update')->middleware(['save']);
    Route::delete('master/products/{product}', 'Master\ProductsController@delete')
        ->name('master.products.delete')->middleware(['save']);
    // 得意先マスタ
    Route::get('master/customers', 'Master\CustomersController@index')
        ->name('master.customers.index')->middleware(['access']);
    Route::get('master/customers/add', 'Master\CustomersController@add')
        ->name('master.customers.add')->middleware(['save']);
    Route::post('master/customers', 'Master\CustomersController@create')
        ->name('master.customers.create')->middleware(['save']);
    Route::get('master/customers/{customer}', 'Master\CustomersController@edit')
        ->name('master.customers.edit')->middleware(['access']);
    Route::post('master/customers/search', 'Master\CustomersController@search')
        ->name('master.customers.search')->middleware(['access']);
    Route::patch('master/customers/{customer}', 'Master\CustomersController@update')
        ->name('master.customers.update')->middleware(['save']);
    Route::delete('master/customers/{customer}', 'Master\CustomersController@delete')
        ->name('master.customers.delete')->middleware(['save']);
    // エンドユーザマスタ
    Route::get('master/end_users', 'Master\EndUsersController@index')
        ->name('master.end_users.index')->middleware(['access']);
    Route::get('master/end_users/add', 'Master\EndUsersController@add')
        ->name('master.end_users.add')->middleware(['save']);
    Route::get('master/end_users/{end_user}', 'Master\EndUsersController@edit')
        ->name('master.end_users.edit')->middleware(['access']);
    Route::get('master/end_users/{end_user}/factories', 'Master\EndUsersController@factories')
        ->name('master.end_users.factories')->middleware(['access']);
    Route::post('master/end_users/search', 'Master\EndUsersController@search')
        ->name('master.end_users.search')->middleware(['access']);
    Route::post('master/end_users', 'Master\EndUsersController@create')
        ->name('master.end_users.create')->middleware(['save']);
    Route::post('master/end_users/factories', 'Master\EndUsersController@createFactory')
        ->name('master.end_users.factories.create')->middleware(['save']);
    Route::patch('master/end_users/{end_user}', 'Master\EndUsersController@update')
        ->name('master.end_users.update')->middleware(['save']);
    Route::delete('master/end_users/{end_user}', 'Master\EndUsersController@delete')
        ->name('master.end_users.delete')->middleware(['save']);
    Route::delete('master/end_users/factories/{end_user_factory}', 'Master\EndUsersController@deleteFactory')
        ->name('master.end_users.factories.delete')->middleware(['save']);
    // 納入先マスタ
    Route::get('master/delivery_destinations', 'Master\DeliveryDestinationsController@index')
        ->name('master.delivery_destinations.index')->middleware(['access']);
    Route::get('master/delivery_destinations/add', 'Master\DeliveryDestinationsController@add')
        ->name('master.delivery_destinations.add')->middleware(['save']);
    Route::get('master/delivery_destinations/{delivery_destination}', 'Master\DeliveryDestinationsController@edit')
        ->name('master.delivery_destinations.edit')->middleware(['access']);
    Route::get(
        'master/delivery_destinations/{delivery_destination}/warehouses',
        'Master\DeliveryDestinationsController@warehouses'
    )
        ->name('master.delivery_destinations.warehouses')->middleware(['access']);
    Route::get(
        'master/delivery_destinations/{delivery_destination}/factory_products',
        'Master\DeliveryDestinationsController@factoryProducts'
    )
        ->name('master.delivery_destinations.factory_products')->middleware(['access']);
    Route::post('master/delivery_destinations/search', 'Master\DeliveryDestinationsController@search')
        ->name('master.delivery_destinations.search')->middleware(['access']);
    Route::post('master/delivery_destinations', 'Master\DeliveryDestinationsController@create')
        ->name('master.delivery_destinations.create')->middleware(['save']);
    Route::patch('master/delivery_destinations/{delivery_destination}', 'Master\DeliveryDestinationsController@update')
        ->name('master.delivery_destinations.update')->middleware(['save']);
    Route::delete('master/delivery_destinations/{delivery_destination}', 'Master\DeliveryDestinationsController@delete')
        ->name('master.delivery_destinations.delete')->middleware(['save']);
    // 納入倉庫マスタ
    Route::post('master/delivery_destinations/delivery_warehouses', 'Master\DeliveryWarehousesController@create')
        ->name('master.delivery_warehouses.create')->middleware(['save']);
    Route::patch(
        'master/delivery_destinations/delivery_warehouses/{delivery_warehouse}',
        'Master\DeliveryWarehousesController@update'
    )
        ->name('master.delivery_warehouses.update')->middleware(['save']);
    Route::delete(
        'master/delivery_destinations/delivery_warehouses/{delivery_warehouse}',
        'Master\DeliveryWarehousesController@delete'
    )
        ->name('master.delivery_warehouses.delete')->middleware(['save']);
    // 納入工場商品マスタ
    Route::post(
        'master/delivery_destinations/delivery_factory_products/{delivery_destination}',
        'Master\DeliveryFactoryProductsController@create'
    )
        ->name('master.delivery_factory_products.create')->middleware(['save']);
    Route::patch(
        'master/delivery_destinations/delivery_factory_products/{delivery_factory_product}',
        'Master\DeliveryFactoryProductsController@update'
    )
        ->name('master.delivery_factory_products.update')->middleware(['save']);
    Route::delete(
        'master/delivery_destinations/delivery_factory_products/{delivery_factory_product}',
        'Master\DeliveryFactoryProductsController@delete'
    )
        ->name('master.delivery_factory_products.delete')->middleware(['save']);
    // 倉庫マスタ
    Route::get('master/warehouses', 'Master\WarehousesController@index')
        ->name('master.warehouses.index')->middleware(['access']);
    Route::get('master/warehouses/add', 'Master\WarehousesController@add')
        ->name('master.warehouses.add')->middleware(['save']);
    Route::get('master/warehouses/{warehouse}', 'Master\WarehousesController@edit')
        ->name('master.warehouses.edit')->middleware(['access']);
    Route::get('master/warehouses/{warehouse}/factory_warehouses', 'Master\WarehousesController@factoryWarehouses')
        ->name('master.warehouses.factory_warehouses')->middleware(['access']);
    Route::get('master/warehouses/{warehouse}/delivery_warehouses', 'Master\WarehousesController@deliveryWarehouses')
        ->name('master.warehouses.delivery_warehouses')->middleware(['access']);
    Route::post('master/warehouses/search', 'Master\WarehousesController@search')
        ->name('master.warehouses.search')->middleware(['access']);
    Route::post('master/warehouses', 'Master\WarehousesController@create')
        ->name('master.warehouses.create')->middleware(['save']);
    Route::patch('master/warehouses/{warehouse}', 'Master\WarehousesController@update')
        ->name('master.warehouses.update')->middleware(['save']);
    Route::delete('master/warehouses/{warehouse}', 'Master\WarehousesController@delete')
        ->name('master.warehouses.delete')->middleware(['save']);
    // リードタイム
    Route::get('master/lead_time', 'Master\DeliveryWarehousesController@index')
        ->name('master.lead_time.index')->middleware(['access']);
    Route::post('master/lead_time/search', 'Master\DeliveryWarehousesController@search')
        ->name('master.lead_time.search')->middleware(['access']);
    // 法人マスタ
    Route::get('master/corporations', 'Master\CorporationsController@index')
        ->name('master.corporations.index')->middleware(['access']);
    Route::get('master/corporations/add', 'Master\CorporationsController@add')
        ->name('master.corporations.add')->middleware(['save']);
    Route::get('master/corporations/{corporation}', 'Master\CorporationsController@edit')
        ->name('master.corporations.edit')->middleware(['access']);
    Route::post('master/corporations/search', 'Master\CorporationsController@search')
        ->name('master.corporations.search')->middleware(['access']);
    Route::post('master/corporations', 'Master\CorporationsController@create')
        ->name('master.corporations.create')->middleware(['save']);
    Route::patch('master/corporations/{corporation}', 'Master\CorporationsController@update')
        ->name('master.corporations.update')->middleware(['save']);
    Route::delete('master/corporations/{corporation}', 'Master\CorporationsController@delete')
        ->name('master.corporations.delete')->middleware(['save']);
    // 工場マスタ
    Route::get('master/factories', 'Master\FactoriesController@index')
        ->name('master.factories.index')->middleware(['access']);
    Route::get('master/factories/add', 'Master\FactoriesController@add')
        ->name('master.factories.add')->middleware(['save']);
    Route::get('master/factories/{factory}/edit', 'Master\FactoriesController@edit')
        ->name('master.factories.edit')->middleware(['access']);
    Route::post('master/factories/search', 'Master\FactoriesController@search')
        ->name('master.factories.search')->middleware(['access']);
    Route::post('master/factories', 'Master\FactoriesController@create')
        ->name('master.factories.create')->middleware(['save']);
    Route::patch('master/factories/{factory}/update', 'Master\FactoriesController@update')
        ->name('master.factories.update')->middleware(['save']);
    Route::delete('master/factories/{factory}/delete', 'Master\FactoriesController@delete')
        ->name('master.factories.delete')->middleware(['save']);
    // 工場レイアウト
    Route::get('master/factories/{factory}/beds', 'Master\FactoryBedsController@index')
        ->name('master.factories.beds')->middleware(['access']);
    Route::patch('master/factories/{factory}/beds', 'Master\FactoryBedsController@update')
        ->name('master.factories.beds.update')->middleware(['save']);
    // 工場倉庫マスタ
    Route::get('master/factories/{factory}/warehouses', 'Master\FactoryWarehousesController@index')
        ->name('master.factories.warehouses')->middleware(['access']);
    Route::post('master/factories/{factory}/warehouses', 'Master\FactoryWarehousesController@create')
        ->name('master.factories.warehouses.create')->middleware(['save']);
    Route::patch('master/factories/{factory}/warehouses', 'Master\FactoryWarehousesController@update')
        ->name('master.factories.warehouses.update')->middleware(['save']);
    Route::delete(
        'master/factories/{factory}/warehouses/{factory_warehouse}',
        'Master\FactoryWarehousesController@delete'
    )
        ->name('master.factories.warehouses.delete')->middleware(['save']);
    // 工場パネルマスタ
    Route::get('master/factories/{factory}/panels', 'Master\FactoryPanelsController@index')
        ->name('master.factories.panels')->middleware(['access']);
    Route::post('master/factories/{factory}/panels', 'Master\FactoryPanelsController@create')
        ->name('master.factories.panels.create')->middleware(['save']);
    Route::delete('master/factories/{factory}/panels/{factory_panel}', 'Master\FactoryPanelsController@delete')
        ->name('master.factories.panels.delete')->middleware(['save']);
    // 工場サイクルパターンマスタ
    Route::get('master/factories/{factory}/cycle_patterns', 'Master\FactoryCyclePatternsController@index')
        ->name('master.factories.cycle_patterns')->middleware(['access']);
    Route::patch('master/factories/{factory}/cycle_patterns', 'Master\FactoryCyclePatternsController@update')
        ->name('master.factories.cycle_patterns.update')->middleware(['save']);
    Route::delete(
        'master/factories/{factory}/cycle_patterns/{factory_cycle_pattern}',
        'Master\FactoryCyclePatternsController@delete'
    )->name('master.factories.cycle_patterns.delete')->middleware(['save']);

    // 工場取扱品種マスタ
    Route::get('master/factories/{factory}/factory_species', 'Master\FactorySpeciesController@index')
        ->name('master.factory_species.index')->middleware(['access']);
    Route::get('master/factories/{factory}/factory_species/add', 'Master\FactorySpeciesController@add')
        ->name('master.factory_species.add')->middleware(['access']);
    Route::post('master/factories/{factory}/factory_species', 'Master\FactorySpeciesController@create')
        ->name('master.factory_species.create')->middleware(['save']);
    Route::get('master/factories/{factory}/factory_species/{factory_species}', 'Master\FactorySpeciesController@edit')
        ->name('master.factory_species.edit')->middleware(['access']);
    Route::patch(
        'master/factories/{factory}/factory_species/{factory_species}',
        'Master\FactorySpeciesController@update'
    )
        ->name('master.factory_species.update')->middleware(['save']);
    Route::delete(
        'master/factories/{factory}/factory_species/{factory_species}',
        'Master\FactorySpeciesController@delete'
    )
        ->name('master.factory_species.delete')->middleware(['save']);
    // 工場取扱商品マスタ
    Route::get('master/factories/{factory}/factory_products', 'Master\FactoryProductsController@index')
        ->name('master.factory_products.index')->middleware(['access']);
    Route::get('master/factories/{factory}/factory_products/add', 'Master\FactoryProductsController@add')
        ->name('master.factory_products.add')->middleware(['access']);
    Route::post('master/factories/{factory}/factory_products', 'Master\FactoryProductsController@create')
        ->name('master.factory_products.create')->middleware(['save']);
    Route::get('master/factories/{factory}/factory_products/{factory_product}', 'Master\FactoryProductsController@edit')
        ->name('master.factory_products.edit')->middleware(['access']);
    Route::patch(
        'master/factories/{factory}/factory_products/{factory_product}',
        'Master\FactoryProductsController@update'
    )
        ->name('master.factory_products.update')->middleware(['save']);
    Route::delete(
        'master/factories/{factory}/factory_products/{factory_product}',
        'Master\FactoryProductsController@delete'
    )
        ->name('master.factory_products.delete')->middleware(['save']);
    // 工場カレンダーマスタ
    Route::get('master/factories/{factory}/factory_rest', 'Master\FactoryRestController@index')
        ->name('master.factory_rest.index')->middleware(['access']);
    Route::post('master/factories/{factory}/factory_rest', 'Master\FactoryRestController@save')
        ->name('master.factory_rest.save')->middleware(['save']);
    // ユーザマスタ
    Route::get('master/users', 'Master\UsersController@index')
        ->name('master.users.index')->middleware(['access']);
    Route::get('master/users/add', 'Master\UsersController@add')
        ->name('master.users.add')->middleware(['save']);
    Route::get('master/users/{user}', 'Master\UsersController@edit')
        ->name('master.users.edit')->middleware(['access']);
    Route::post('master/users/search', 'Master\UsersController@search')
        ->name('master.users.search')->middleware(['access']);
    Route::post('master/users/permissions', 'Master\UsersController@permissions')
        ->name('master.users.permissions')->middleware(['access']);
    Route::post('master/users', 'Master\UsersController@create')
        ->name('master.users.create')->middleware(['save']);
    Route::patch('master/users/{user}', 'Master\UsersController@update')
        ->name('master.users.update')->middleware(['save']);
    Route::patch('master/users/{user}/reset', 'Master\UsersController@reset')
        ->name('master.users.reset')->middleware(['save']);
    Route::delete('master/users/{user}', 'Master\UsersController@delete')
        ->name('master.users.delete')->middleware(['save']);
    // 品種マスタ
    Route::get('master/species', 'Master\SpeciesController@index')
        ->name('master.species.index')->middleware(['access']);
    Route::post('master/species/search', 'Master\SpeciesController@search')
        ->name('master.species.search')->middleware(['access']);
    Route::post('master/species', 'Master\SpeciesController@create')
        ->name('master.species.create')->middleware(['save']);
    Route::delete('master/species/{species}', 'Master\SpeciesController@delete')
        ->name('master.species.delete')->middleware(['save']);
    Route::patch('master/species/{species}', 'Master\SpeciesController@update')
        ->name('master.species.update')->middleware(['save']);
    // 運送会社マスタ
    Route::get('master/transport_companies', 'Master\TransportCompaniesController@index')
        ->name('master.transport_companies.index')->middleware(['access']);
    Route::post('master/transport_companies/search', 'Master\TransportCompaniesController@search')
        ->name('master.transport_companies.search')->middleware(['access']);
    Route::post('master/transport_companies/create', 'Master\TransportCompaniesController@create')
        ->name('master.transport_companies.create')->middleware(['save']);
    Route::get('master/transport_companies/add', 'Master\TransportCompaniesController@add')
        ->name('master.transport_companies.add')->middleware(['save']);
    Route::get('master/transport_companies/{transport_company}', 'Master\TransportCompaniesController@edit')
        ->name('master.transport_companies.edit')->middleware(['access']);
    Route::patch('master/transport_companies/{transport_company}', 'Master\TransportCompaniesController@update')
        ->name('master.transport_companies.update')->middleware(['save']);
    Route::delete('master/transport_companies/{transport_company}', 'Master\TransportCompaniesController@delete')
        ->name('master.transport_companies.delete')->middleware(['save']);
    // 集荷時間マスタ
    Route::get(
        'master/transport_companies/{transport_company}/collection_times',
        'Master\CollectionTimeController@index'
    )
        ->name('master.collection_times.index')->middleware(['access']);
    Route::post(
        'master/transport_companies/{transport_company}/collection_times',
        'Master\CollectionTimeController@create'
    )
        ->name('master.collection_times.create')->middleware(['save']);
    Route::patch(
        'master/transport_companies/{transport_company}/collection_times/{collection_time}',
        'Master\CollectionTimeController@update'
    )
        ->name('master.collection_times.update')->middleware(['save']);
    Route::delete(
        'master/transport_companies/{transport_company}/collection_times/{collection_time}',
        'Master\CollectionTimeController@delete'
    )
        ->name('master.collection_times.delete')->middleware(['save']);
    // カレンダーマスタ
    Route::get('master/calendars', 'Master\CalendarsController@index')
        ->name('master.calendars.index')->middleware(['access']);
    Route::post('master/calendars', 'Master\CalendarsController@save')
        ->name('master.calendars.save')->middleware(['save']);
    Route::delete('master/calendars/{calendar}', 'Master\CalendarsController@delete')
        ->name('master.calendars.delete')->middleware(['save']);

    // 生産計画メニュー
    // 生産シミュレーション
    Route::get('plan/growth_simulation', 'Plan\GrowthSimulationController@index')
        ->name('plan.growth_simulation.index')->middleware(['access']);
    Route::post('plan/growth_simulation/search', 'Plan\GrowthSimulationController@search')
        ->name('plan.growth_simulation.search')->middleware(['access']);
    Route::get('plan/growth_simulation/add', 'Plan\GrowthSimulationController@add')
        ->name('plan.growth_simulation.add')->middleware(['save']);
    Route::post('plan/growth_simulation/add/search', 'Plan\GrowthSimulationController@addSearch')
        ->name('plan.growth_simulation.add.search')->middleware(['save']);
    Route::post('plan/growth_simulation', 'Plan\GrowthSimulationController@create')
        ->name('plan.growth_simulation.create')->middleware(['save']);
    Route::patch('plan/growth_simulation/{growth_simulation}/lock', 'Plan\GrowthSimulationController@lock')
        ->name('plan.growth_simulation.lock')->middleware(['save']);
    Route::patch('plan/growth_simulation/{growth_simulation}/unlock', 'Plan\GrowthSimulationController@unlock')
        ->name('plan.growth_simulation.unlock')->middleware(['save']);
    Route::get('plan/growth_simulation/{growth_simulation}', 'Plan\GrowthSimulationController@edit')
        ->name('plan.growth_simulation.edit')->middleware(['access']);
    Route::post('plan/growth_simulation/{growth_simulation}/search', 'Plan\GrowthSimulationController@editSearch')
        ->name('plan.growth_simulation.edit.search')->middleware(['access']);
    Route::patch('plan/growth_simulation/{growth_simulation}/change-name', 'Plan\GrowthSimulationController@changeName')
        ->name('plan.growth_simulation.change-name')->middleware(['save']);
    Route::post('plan/growth_simulation/{growth_simulation}', 'Plan\GrowthSimulationController@update')
        ->name('plan.growth_simulation.update')->middleware(['save']);
    Route::delete('plan/growth_simulation/{growth_simulation}', 'Plan\GrowthSimulationController@delete')
        ->name('plan.growth_simulation.delete')->middleware(['save']);
    Route::get(
        'plan/growth_simulation/{growth_simulation}/check-bed-number',
        'Plan\GrowthSimulationController@checkBedNumber'
    )
        ->name('plan.growth_simulation.check_bed_number')->middleware(['save']);
    Route::post('plan/growth_simulation/{growth_simulation}/fix', 'Plan\GrowthSimulationController@fix')
        ->name('plan.growth_simulation.fix')->middleware(['save']);
    // 生産シミュレーション確定
    Route::get('plan/growth_simulation_fixed', 'Plan\GrowthSimulationController@indexFixed')
        ->name('plan.growth_simulation_fixed.index')->middleware(['access']);
    Route::post('plan/growth_simulation_fixed/search', 'Plan\GrowthSimulationController@searchFixed')
        ->name('plan.growth_simulation_fixed.search')->middleware(['access']);
    // 各階栽培株数一覧・合計表処理
    Route::get(
        'plan/growth_simulation/planned_cultivation_status_work/{growth_simulation}/{simulation_date}',
        'Plan\PlannedCultivationStatusWorkController@index'
    )
        ->name('plan.planned_cultivation_status_work.index')->middleware(['access']);
    Route::get(
        'plan/growth_simulation/planned_cultivation_status_work/{growth_simulation}/{simulation_date}/sum',
        'Plan\PlannedCultivationStatusWorkController@sum'
    )
        ->name('plan.planned_cultivation_status_work.sum')->middleware(['access']);
    Route::get(
        'plan/growth_simulation/planned_cultivation_status_work/{growth_simulation}/{simulation_date}/export',
        'Plan\PlannedCultivationStatusWorkController@export'
    )
        ->name('plan.planned_cultivation_status_work.export')->middleware(['access']);
    Route::patch(
        'plan/growth_simulation/planned_cultivation_status_work/{growth_simulation}/{simulation_date}',
        'Plan\PlannedCultivationStatusWorkController@save'
    )
        ->name('plan.planned_cultivation_status_work.save')->middleware(['save']);
    // 栽培パネル配置図
    Route::get(
        'plan/growth_simulation/planned_arrangement_status_work/{growth_simulation}/{simulation_date}',
        'Plan\PlannedArrangementStatusWorkController@index'
    )
        ->name('plan.planned_arrangement_status_work.index')->middleware(['access']);
    Route::get(
        'plan/growth_simulation/planned_arrangement_status_work/{growth_simulation}/{simulation_date}/export',
        'Plan\PlannedArrangementStatusWorkController@export'
    )
        ->name('plan.planned_arrangement_status_work.export')->middleware(['access']);
    Route::get(
        'plan/growth_simulation/planned_arrangement_status_work/{growth_simulation}/{simulation_date}/{floor}',
        'Plan\PlannedArrangementStatusWorkController@detail'
    )
        ->name('plan.planned_arrangement_status_work.detail')->middleware(['access']);
    Route::get(
        'plan/growth_simulation/planned_arrangement_status_work/{growth_simulation}/{simulation_date}/{floor}/export',
        'Plan\PlannedArrangementStatusWorkController@exportDetail'
    )
        ->name('plan.planned_arrangement_status_work.detail.export')->middleware(['access']);
    Route::patch(
        'plan/growth_simulation/planned_arrangement_status_work/{growth_simulation}/{simulation_date}',
        'Plan\PlannedArrangementStatusWorkController@save'
    )
        ->name('plan.planned_arrangement_status_work.save')->middleware(['save']);
    // ベッド状況確認
    Route::get('plan/bed_states', 'Plan\BedStatesController@index')
        ->name('plan.bed_states.index')->middleware(['access']);
    Route::post('plan/bed_states/search', 'Plan\BedStatesController@search')
        ->name('plan.bed_states.search')->middleware(['access']);
    Route::post('plan/bed_states', 'Plan\BedStatesController@create')
        ->name('plan.bed_states.create')->middleware(['access']);
    Route::get('plan/bed_states/{bed_state}/cultivation_states', 'Plan\BedStatesController@cultivationStates')
        ->name('plan.bed_states.cultivation_states.index')->middleware(['access']);
    Route::get('plan/bed_states/{bed_state}/cultivation_states/sum', 'Plan\BedStatesController@cultivationStatesSum')
        ->name('plan.bed_states.cultivation_states.sum')->middleware(['access']);
    Route::get(
        'plan/bed_states/{bed_state}/cultivation_states/export',
        'Plan\BedStatesController@exportCultivationStates'
    )
        ->name('plan.bed_states.cultivation_states.export')->middleware(['access']);
    Route::get(
        'plan/bed_states/{bed_state}/arrangement_states/{working_date}',
        'Plan\BedStatesController@arrangementStates'
    )
        ->name('plan.bed_states.arrangement_states.index')->middleware(['access']);
    Route::get(
        'plan/bed_states/{bed_state}/arrangement_states/{working_date}/export',
        'Plan\BedStatesController@exportArrangementStates'
    )
        ->name('plan.bed_states.arrangement_states.export')->middleware(['access']);
    Route::get(
        'plan/bed_states/{bed_state}/arrangement_states/{working_date}/{floor}',
        'Plan\BedStatesController@arrangementStatesDetail'
    )
        ->name('plan.bed_states.arrangement_states.detail')->middleware(['access']);
    Route::get(
        'plan/bed_states/{bed_state}/arrangement_states/{working_date}/{floor}/export',
        'Plan\BedStatesController@exportArrangementStatesDetail'
    )
        ->name('plan.bed_states.arrangement_states.detail.export')->middleware(['access']);
    Route::delete('plan/bed_states/{bed_state}', 'Plan\BedStatesController@delete')
        ->name('plan.bed_states.delete')->middleware(['access']);
    // 生産・販売管理表処理
    Route::get('plan/growth_sale_management', 'Plan\GrowthSaleManagementController@index')
        ->name('plan.growth_sale_management.index')->middleware(['access']);
    Route::get('plan/growth_sale_management/export', 'Plan\GrowthSaleManagementController@export')
        ->name('plan.growth_sale_management.export')->middleware(['access']);
    Route::post('plan/growth_sale_management/import', 'Plan\GrowthSaleManagementController@import')
        ->name('plan.growth_sale_management.import')->middleware(['save']);
    // 生産・販売管理表サマリー処理
    Route::get('plan/growth_sale_management_summary', 'Plan\GrowthSaleManagementSummaryController@index')
        ->name('plan.growth_sale_management_summary.index')->middleware(['access']);
    Route::get('plan/growth_sale_management_summary/factories', 'Plan\GrowthSaleManagementSummaryController@factories')
        ->name('plan.growth_sale_management_summary.factories')->middleware(['access']);
    Route::get(
        'plan/growth_sale_management_summary/factory_species',
        'Plan\GrowthSaleManagementSummaryController@factorySpecies'
    )
        ->name('plan.growth_sale_management_summary.factory_species')->middleware(['access']);
    Route::get(
        'plan/growth_sale_management_summary/delivery_destination',
        'Plan\GrowthSaleManagementSummaryController@deliveryDestination'
    )
        ->name('plan.growth_sale_management_summary.delivery_destination')->middleware(['access']);
    Route::post('plan/growth_sale_management_summary/search', 'Plan\GrowthSaleManagementSummaryController@search')
        ->name('plan.growth_sale_management_summary.search')->middleware(['access']);
    // 生産計画表
    Route::get('plan/growth_planned_table', 'Plan\GrowthPlannedTableController@index')
        ->name('plan.growth_planned_table.index')->middleware(['access']);
    Route::get('plan/growth_planned_table/export', 'Plan\GrowthPlannedTableController@export')
        ->name('plan.growth_planned_table.export')->middleware(['access']);
    // 施設利用状況一覧
    Route::get('plan/facility_status_list', 'Plan\FacilityStatusListController@index')
        ->name('plan.facility_status_list.index')->middleware(['access']);
    Route::get('plan/facility_status_list/export', 'Plan\FacilityStatusListController@export')
        ->name('plan.facility_status_list.export')->middleware(['access']);

    // 受注機能
    // フォーキャストExcel取込
    Route::get('order/order_forecasts', 'Order\OrderForecastsController@index')
        ->name('order.order_forecasts.index')->middleware(['access']);
    Route::get('order/order_forecasts/export', 'Order\OrderForecastsController@export')
        ->name('order.order_forecasts.export')->middleware(['access']);
    Route::post('order/order_forecasts/import', 'Order\OrderForecastsController@import')
        ->name('order.order_forecasts.import')->middleware(['save']);
    // 注文入力
    Route::get('order/order_input', 'Order\OrderInputController@index')
        ->name('order.order_input.index')->middleware(['access']);
    Route::post('order/order_input/search', 'Order\OrderInputController@search')
        ->name('order.order_input.search')->middleware(['access']);
    Route::post('order/order_input', 'Order\OrderInputController@create')
        ->name('order.order_input.create')->middleware(['save']);
    Route::patch('order/order_input/{order}', 'Order\OrderInputController@update')
        ->name('order.order_input.update')->middleware(['save']);
    Route::delete('order/order_input/{order}', 'Order\OrderInputController@delete')
        ->name('order.order_input.delete')->middleware(['save']);
    // 注文一覧
    Route::get('order/order_list', 'Order\OrderListController@index')
        ->name('order.order_list.index')->middleware(['access']);
    Route::post('order/order_list/search', 'Order\OrderListController@search')
        ->name('order.order_list.search')->middleware(['access']);
    Route::post('order/order_list/export', 'Order\OrderListController@export')
        ->name('order.order_list.export')->middleware(['access']);
    Route::post('order/order_list/match', 'Order\OrderListController@match')
        ->name('order.order_list.match')->middleware(['save']);
    Route::post('order/order_list/save-slip', 'Order\OrderListController@saveSlip')
        ->name('order.order_list.save-slip')->middleware(['save']);
    Route::get('order/order_list/search-fixed-orders', 'Order\OrderListController@searchFixedOrders')
        ->middleware(['access']);
    Route::post('order/order_list/{order}/link', 'Order\OrderListController@link')
        ->name('order.order_list.link')->middleware(['save']);
    Route::delete('order/order_list/{order}/link', 'Order\OrderListController@cancelLink')
        ->name('order.order_list.link.cancel')->middleware(['save']);
    Route::get('order/order_list/{order}', 'Order\OrderListController@edit')
        ->name('order.order_list.edit')->middleware(['access']);
    Route::patch('order/order_list/{order}', 'Order\OrderListController@update')
        ->name('order.order_list.update')->middleware(['save']);
    Route::patch('order/order_list/{order}/cancel', 'Order\OrderListController@cancel')
        ->name('order.order_list.cancel')->middleware(['save']);
    // 返品入力
    Route::get('order/returned_products', 'Order\ReturnedProductsController@index')
        ->name('order.returned_products.index')->middleware(['access']);
    Route::post('order/returned_products/search', 'Order\ReturnedProductsController@search')
        ->name('order.returned_products.search')->middleware(['access']);
    Route::post('order/returned_products/{order}', 'Order\ReturnedProductsController@create')
        ->name('order.returned_products.create')->middleware(['save', 'stocktaking']);
    Route::patch('order/returned_products/{order}', 'Order\ReturnedProductsController@update')
        ->name('order.returned_products.update')->middleware(['save', 'stocktaking']);
    // ホワイトボード情報参照処理
    Route::get('order/whiteboard_reference', 'Order\WhiteboardReferenceController@index')
        ->name('order.whiteboard_reference.index')->middleware(['access']);
    Route::post('order/whiteboard_reference/search', 'Order\WhiteboardReferenceController@search')
        ->name('order.whiteboard_reference.search')->middleware(['access']);
    Route::post('order/whiteboard_reference/export', 'Order\WhiteboardReferenceController@export')
        ->name('order.whiteboard_reference.export')->middleware(['access']);
    //注文書Excel取込
    Route::get('order/purchase_order_excel_import', 'Order\PurchaseOrderExcelImportController@index')
        ->name('order.purchase_order_excel_import.index')->middleware(['access']);
    Route::post('order/purchase_order_excel_import/import', 'Order\PurchaseOrderExcelImportController@import')
        ->name('order.purchase_order_excel_import.import')->middleware(['save']);

    // 出荷機能
    // 製品化実績一覧
    Route::get('shipment/productized_results', 'Shipment\ProductizedResultsController@index')
        ->name('shipment.productized_results.index')->middleware(['access']);
    Route::post('shipment/productized_results/search', 'Shipment\ProductizedResultsController@search')
        ->name('shipment.productized_results.search')->middleware(['access']);
    // 製品化実績入力
    Route::get(
        'shipment/productized_results/input/{factory}/{species}/{harvesting_date}',
        'Shipment\ProductizedResultsController@input'
    )
        ->name('shipment.productized_results.input')->middleware(['access', 'stocktaking']);
    Route::post(
        'shipment/productized_results/input/{factory}/{species}/{harvesting_date}',
        'Shipment\ProductizedResultsController@save'
    )
        ->name('shipment.productized_results.save')->middleware(['save', 'stocktaking']);
    // 在庫引当
    Route::get(
        'shipment/productized_results/product_allocations/{factory}/{species}/{harvesting_date}',
        'Shipment\ProductAllocationsController@index'
    )
        ->name('shipment.product_allocations.index')->middleware(['access', 'stocktaking']);
    Route::post(
        'shipment/productized_results/product_allocations/{factory}/{species}/{harvesting_date}',
        'Shipment\ProductAllocationsController@save'
    )
        ->name('shipment.product_allocations.save')->middleware(['save', 'stocktaking']);
    // 出荷データ出力
    Route::get('shipment/shipment_data_export', 'Shipment\ShipmentDataExportController@index')
        ->name('shipment.shipment_data_export.index')->middleware(['access']);
    Route::get('shipment/shipment_data_export/export', 'Shipment\ShipmentDataExportController@export')
        ->name('shipment.shipment_data_export.export')->middleware(['access']);
    // 出荷確定
    Route::get('shipment/shipment_fix', 'Shipment\ShipmentFixController@index')
        ->name('shipment.shipment_fix.index')->middleware(['access']);
    Route::post('shipment/shipment_fix/search', 'Shipment\ShipmentFixController@search')
        ->name('shipment.shipment_fix.search')->middleware(['access']);
    Route::patch('shipment/shipment_fix', 'Shipment\ShipmentFixController@fix')
        ->name('shipment.shipment_fix.fix')->middleware(['save', 'stocktaking']);
    // 出荷作業帳票出力
    Route::get('shipment/form_output', 'Shipment\FormOutputController@index')
        ->name('shipment.form_output.index')->middleware(['access']);
    Route::post('shipment/form_output/search', 'Shipment\FormOutputController@search')
        ->name('shipment.form_output.search')->middleware(['access']);
    Route::post('shipment/form_output/download', 'Shipment\FormOutputController@download')
        ->name('shipment.form_output.download')->middleware(['access']);
    // 請求書出力
    Route::get('shipment/invoices/export', 'Shipment\InvoicesController@indexExport')
        ->name('shipment.invoices.export.index')->middleware(['access']);
    Route::post('shipment/invoices/export', 'Shipment\InvoicesController@export')
        ->name('shipment.invoices.export')->middleware(['access']);
    // 請求書締め
    Route::get('shipment/invoices', 'Shipment\InvoicesController@index')
        ->name('shipment.invoices.index')->middleware(['access']);
    Route::post('shipment/invoices/search', 'Shipment\InvoicesController@search')
        ->name('shipment.invoices.search')->middleware(['access']);
    Route::post('shipment/invoices', 'Shipment\InvoicesController@fix')
        ->name('shipment.invoices.fix')->middleware(['save']);
    Route::put('shipment/invoices/{invoice}/cancel', 'Shipment\InvoicesController@cancel')
        ->name('shipment.invoices.cancel')->middleware(['save']);
    // 集荷依頼書
    Route::get('shipment/collection_request', 'Shipment\CollectionRequestController@index')
        ->name('shipment.collection_request.index')->middleware(['access']);
    Route::post('shipment/collection_request/search', 'Shipment\CollectionRequestController@search')
        ->name('shipment.collection_request.search')->middleware(['access']);
    Route::post('shipment/collection_request/export', 'Shipment\CollectionRequestController@export')
        ->name('shipment.collection_request.export')->middleware(['access']);
    Route::post('shipment/collection_request/save', 'Shipment\CollectionRequestController@save')
        ->name('shipment.collection_request.save')->middleware(['save']);

    // 工場生産活動機能
    // 作業指示書
    Route::get('factory_production_work/work_instruction', 'FactoryProductionWork\WorkInstructionController@index')
        ->name('factory_production_work.work_instruction.index')->middleware(['access']);
    Route::get(
        'factory_production_work/work_instruction/export',
        'FactoryProductionWork\WorkInstructionController@export'
    )
        ->name('factory_production_work.work_instruction.export')->middleware(['access']);
    // 活動実績
    Route::get('factory_production_work/activity_results', 'FactoryProductionWork\ActivityResultsController@index')
        ->name('factory_production_work.activity_results.index')->middleware(['access']);
    Route::post(
        'factory_production_work/activity_results/search',
        'FactoryProductionWork\ActivityResultsController@search'
    )
        ->name('factory_production_work.activity_results.search')->middleware(['access']);
    Route::get(
        'factory_production_work/activity_results/{factory_species}',
        'FactoryProductionWork\ActivityResultsController@edit'
    )
        ->name('factory_production_work.activity_results.edit')->middleware(['access']);
    Route::post(
        'factory_production_work/activity_results/{factory_species}/panel_search',
        'FactoryProductionWork\ActivityResultsController@panelSearch'
    )
        ->name('factory_production_work.activity_results.panel_search')->middleware(['access']);
    Route::patch(
        'factory_production_work/activity_results/{factory_species}',
        'FactoryProductionWork\ActivityResultsController@update'
    )
        ->name('factory_production_work.activity_results.update')->middleware(['save']);

    // 在庫
    // 在庫サマリー
    Route::get('stock/stocks.summary', 'Stock\StocksController@summary')
        ->name('stock.stocks.summary.index')->middleware(['access']);
    Route::post('stock/stocks.summary/search', 'Stock\StocksController@searchSummary')
        ->name('stock.stocks.summary.search')->middleware(['access']);
    // 在庫一覧
    Route::get('stock/stocks', 'Stock\StocksController@index')
        ->name('stock.stocks.index')->middleware(['access']);
    Route::post('stock/stocks/search', 'Stock\StocksController@search')
        ->name('stock.stocks.search')->middleware(['access']);
    Route::post('stock/stocks/export', 'Stock\StocksController@export')
        ->name('stock.stocks.export')->middleware(['access']);
    // 在庫移動
    Route::get('stock/stocks/{stock}/move', 'Stock\StocksController@move')
        ->name('stock.stocks.move')->middleware(['save', 'stocktaking']);
    Route::patch('stock/stocks/{stock}/move', 'Stock\StocksController@saveMoving')
        ->name('stock.stocks.move')->middleware(['save', 'stocktaking']);
    Route::get('stock/stocks/{stock}/move/export', 'Stock\StocksController@exportMoved')
        ->name('stock.stocks.move.export')->middleware(['access']);
    // 在庫調整
    Route::get('stock/stocks/{stock}/adjust', 'Stock\StocksController@adjust')
        ->name('stock.stocks.adjust')->middleware(['save', 'stocktaking']);
    Route::patch('stock/stocks/{stock}/adjust', 'Stock\StocksController@saveAdjusting')
        ->name('stock.stocks.adjust')->middleware(['save', 'stocktaking']);
    // 廃棄登録
    Route::get('stock/stocks.dispose', 'Stock\StocksController@dispose')
        ->name('stock.stocks.dispose.index')->middleware(['access']);
    Route::post('stock/stocks.dispose/search', 'Stock\StocksController@searchDisposed')
        ->name('stock.stocks.dispose.search')->middleware(['access']);
    Route::patch('stock/stocks.dispose', 'Stock\StocksController@saveDisposing')
        ->name('stock.stocks.dispose')->middleware(['save', 'stocktaking']);
    Route::post('stock/stocks.dispose/export', 'Stock\StocksController@exportDisposed')
        ->name('stock.stocks.dispose.export')->middleware(['access']);
    // 在庫棚卸
    Route::get('stock/stocktaking', 'Stock\StocktakingController@index')
        ->name('stock.stocktaking.index')->middleware(['access']);
    Route::get('stock/stocktaking/export', 'Stock\StocktakingController@export')
        ->name('stock.stocktaking.export')->middleware(['access']);
    Route::get('stock/stocktaking/{stocktaking}/export', 'Stock\StocktakingController@exportTransition')
        ->name('stock.stocktaking.export.transition')->middleware(['access']);
    Route::post('stock/stocktaking/search', 'Stock\StocktakingController@search')
        ->name('stock.stocktaking.search')->middleware(['access']);
    Route::post('stock/stocktaking', 'Stock\StocktakingController@start')
        ->name('stock.stocktaking.start')->middleware(['save']);
    Route::delete('stock/stocktaking/{stocktaking}', 'Stock\StocktakingController@refresh')
        ->name('stock.stocktaking.refresh')->middleware(['save']);
    Route::patch('stock/stocktaking/{stocktaking}/keep', 'Stock\StocktakingController@keep')
        ->name('stock.stocktaking.keep')->middleware(['save']);
    Route::patch('stock/stocktaking/{stocktaking}/restart', 'Stock\StocktakingController@restart')
        ->name('stock.stocktaking.restart')->middleware(['save']);
    Route::patch('stock/stocktaking/{stocktaking}', 'Stock\StocktakingController@complete')
        ->name('stock.stocktaking.complete')->middleware(['save']);
    // 在庫状況確認
    Route::get('stock/stock_states', 'Stock\StockStatesController@index')
        ->name('stock.stock_states.index')->middleware(['access']);
    Route::get('stock/stock_states/export', 'Stock\StockStatesController@export')
        ->name('stock.stock_states.export')->middleware(['access']);
    // 在庫履歴一覧
    Route::get('stock/stock_histories', 'Stock\StockHistoriesController@index')
        ->name('stock.stock_histories.index')->middleware(['access']);
    Route::get('stock/stock_histories/export', 'Stock\StockHistoriesController@export')
        ->name('stock.stock_histories.export')->middleware(['access']);
    
    // 追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記
    Route::get('stock/stocks.summary', 'Stock\StocksController@summary')
        ->name('stock.stocks.summary.index')->middleware(['access']);
    Route::post('stock/stocks.summary/search', 'Stock\StocksController@searchSummary')
        ->name('stock.stocks.summary.search')->middleware(['access']);
    // 外部システム連携処理
    Route::get('external_integration/jccores', 'ExternalIntegration\JccoresController@index')
    ->name('external_integration.jccores.index')->middleware(['access']);
    Route::post('external_integration/jccores/search', 'ExternalIntegration\JccoresController@search')
    ->name('external_integration.jccores.search')->middleware(['access']);
    Route::get('external_integration/jccores/volume', 'ExternalIntegration\Jccorescontroller@volume')
    ->name('external_integration.jccores.volume')->middleware(['access']);
    Route::get('external_integration/jccores/consumption', 'ExternalIntegration\Jccorescontroller@consumption')
    ->name('external_integration.jccores.consumption')->middleware(['access']);
    Route::get('external_integration/jccores/receipt', 'ExternalIntegration\Jccorescontroller@receipt')
    ->name('external_integration.jccores.receipt')->middleware(['access']);
    Route::get('external_integration/jccores/zip', 'ExternalIntegration\Jccorescontroller@zip')
    ->name('external_integration.jccores.zip')->middleware(['access']);
    //工場JCCOREs用工場変換コード
    Route::get('master/factories/{factory}/jccores', 'Master\FactoryJccoresController@index')
    ->name('master.factories.jccores')->middleware(['access']);
    Route::post('master/factories/{factory}/jccores/update', 'Master\FactoryJccoresController@update')
    ->name('master.factories.jccores.update');
    // 追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記追記
});
