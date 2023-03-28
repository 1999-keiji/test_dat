<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('search-delivery-destinations', 'Master\DeliveryDestinationsController@searchDeliveryDestinations');
Route::get('search-end-users', 'Master\EndUsersController@searchEndUsers');
Route::get('get-factory-products', 'Master\FactoryProductsController@getFactoryProducts');
Route::get(
    'get-packaging-styles-with-factory-code-and-species-code',
    'Master\FactoryProductsController@getPackagingStylesWithFactoryCodeAndSpeciesCode'
);
Route::get('get-products', 'Master\ProductsController@getProducts');
Route::get('get-product-prices', 'Master\ProductsController@getProductPrices');
Route::get('get-product-special-prices', 'Master\ProductsController@getProductSpecialPrices');
Route::get('get-factory-species', 'Master\FactorySpeciesController@getFactorySpecies');
Route::get('get-species-with-factory-code', 'Master\FactorySpeciesController@getSpeciesWithFactoryCode');
Route::get('simulate-growing', 'Master\FactorySpeciesController@simulateGrowing');
Route::get('get-factory-cycle-pattern-items', 'Master\FactoryCyclePatternsController@getFactoryCyclePatternItems');
Route::get(
    'get-shipping-date',
    'Master\DeliveryWarehousesController@getShippingDateByDeliveryDestinationAndFactory'
);
Route::get('get-delivery-factory-products', 'Master\DeliveryFactoryProductsController@getDeliveryFactoryProducts');
Route::get('get-applied-factory-product-price', 'Master\FactoryProductsController@getAppliedFactoryProductPrice');
Route::get(
    'get-applied-factory-product-special-price',
    'Master\DeliveryFactoryProductsController@getAppliedFactoryProductSpecialPrice'
);
Route::get(
    'get-collection-times-by-trasport-company',
    'Master\CollectionTimeController@getCollectionTimesByTransportCompany'
);
Route::get('get-warehouses-with-factory-code', 'Master\FactoryWarehousesController@getWarehousesWithFactoryCode');
Route::get('search-users', 'Master\UsersController@searchUsers');
