<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composers([
            ViewComposers\Master\ProductsComposer::class => ['master.products.*'],
            ViewComposers\Master\CustomersComposer::class => ['master.customers.*'],
            ViewComposers\Master\EndUsersComposer::class => ['master.end_users.*'],
            ViewComposers\Master\DeliveryDestinationsComposer::class => ['master.delivery_destinations.*'],
            ViewComposers\Master\CorporationsComposer::class => ['master.corporations.*'],
            ViewComposers\Master\UsersComposer::class => ['master.users.*'],
            ViewComposers\Master\FactorySpeciesComposer::class => ['master.factory_species.*'],
            ViewComposers\Master\FactoryProductsComposer::class => ['master.factory_products.*'],
            ViewComposers\Master\WarehousesComposer::class => ['master.warehouses.*'],
            ViewComposers\Master\FactoriesComposer::class => ['master.factories.*'],
            ViewComposers\Master\DeliveryWarehousesComposer::class => ['master.delivery_warehouses.*'],
            ViewComposers\Master\SpeciesComposer::class => ['master.species.*'],
            ViewComposers\Master\TransportCompaniesComposer::class => ['master.transport_companies.*'],
            ViewComposers\Master\CalendarsComposer::class => ['master.calendars.*'],
            ViewComposers\Plan\GrowthSimulationComposer::class => [
                'plan.growth_simulation.*',
                'plan.growth_simulation_fixed.*',
                'plan.growth_planned_table.index',
                'plan.facility_status_list.*'
            ],
            ViewComposers\Plan\GrowthSaleManagementComposer::class =>
                ['plan.growth_sale_management_summary.*', 'plan.growth_sale_management.index'],
            ViewComposers\Order\OrderForecastsComposer::class => ['order.order_forecasts.*'],
            ViewComposers\Order\OrderListComposer::class => ['order.order_list.*'],
            ViewComposers\Order\OrderInputComposer::class => ['order.order_input.*'],
            ViewComposers\Order\WhiteboardReferenceComposer::class => ['order.whiteboard_reference.*'],
            ViewComposers\Order\ReturnedProductsComposer::class => ['order.returned_products.*'],
            ViewComposers\Shipment\ProductizedResultsComposer::class => ['shipment.productized_results.*'],
            ViewComposers\Shipment\ShipmentDataExportComposer::class => ['shipment.shipment_data_export.index'],
            ViewComposers\Shipment\ProductAllocationsComposer::class => ['shipment.product_allocations.index'],
            ViewComposers\Shipment\InvoicesComposer::class => ['shipment.invoices.*'],
            ViewComposers\FactoryProductionWork\WorkInstructionComposer::class
                => ['factory_production_work.work_instruction.*'],
            ViewComposers\FactoryProductionWork\ActivityResultsComposer::class
                => ['factory_production_work.activity_results.*'],
            ViewComposers\Shipment\CollectionRequestComposer::class => ['shipment.collection_request.*'],
            ViewComposers\Shipment\ShipmentFixComposer::class => ['shipment.shipment_fix.*'],
            ViewComposers\Shipment\FormOutputComposer::class => ['shipment.form_output.*'],
            ViewComposers\Stock\StocksComposer::class => ['stock.stocks.*'],
            ViewComposers\Stock\StocktakingComposer::class => ['stock.stocktaking.*'],
            ViewComposers\Stock\StockStatesComposer::class => ['stock.stock_states.*'],
            ViewComposers\Stock\StockHistoriesComposer::class => ['stock.stock_histories.*']
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
