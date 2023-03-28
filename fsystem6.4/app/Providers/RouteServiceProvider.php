<?php

namespace App\Providers;

use InvalidArgumentException;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\Master\CollectionTime;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\DeliveryWarehouse;
use App\Models\Master\EndUser;
use App\Models\Master\EndUserFactory;
use App\Models\Master\FactoryCyclePattern;
use App\Models\Master\FactoryPanel;
use App\Models\Master\FactoryProduct;
use App\Models\Master\FactorySpecies;
use App\Models\Master\FactoryWarehouse;
use App\Models\Plan\BedState;
use App\Models\Plan\GrowthSimulation;
use App\Models\Shipment\ProductizedResult;
use App\Models\Stock\Stocktaking;
use App\ValueObjects\Date\HarvestingDate;
use App\ValueObjects\Date\SimulationDate;
use App\ValueObjects\Date\WorkingDate;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::bind('end_user', function ($joined_primary_keys) {
            $end_user = new EndUser();
            $query = $end_user->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($end_user->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $end_user = $query->first();
            if (is_null($end_user)) {
                abort(404);
            }

            return $end_user;
        });

        Route::bind('end_user_factory', function ($joined_primary_keys) {
            $end_user_factory = new EndUserFactory();
            $query = $end_user_factory->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($end_user_factory->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $end_user_factory = $query->first();
            if (is_null($end_user_factory)) {
                abort(404);
            }

            return $end_user_factory;
        });

        Route::bind('delivery_warehouse', function ($joined_primary_keys) {
            $delivery_warehouse = new DeliveryWarehouse();
            $query = $delivery_warehouse->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($delivery_warehouse->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $delivery_warehouse = $query->first();
            if (is_null($delivery_warehouse)) {
                abort(404);
            }

            return $delivery_warehouse;
        });

        Route::bind('delivery_factory_product', function ($joined_primary_keys) {
            $delivery_facotry_product = new DeliveryFactoryProduct();
            $query = $delivery_facotry_product->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($delivery_facotry_product->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $delivery_facotry_product = $query->first();
            if (is_null($delivery_facotry_product)) {
                abort(404);
            }

            return $delivery_facotry_product;
        });

        Route::bind('factory_warehouse', function ($joined_primary_keys) {
            $factory_warehouse = new FactoryWarehouse();
            $query = $factory_warehouse->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($factory_warehouse->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $factory_warehouse = $query->first();
            if (is_null($factory_warehouse)) {
                abort(404);
            }

            return $factory_warehouse;
        });

        Route::bind('factory_panel', function ($joined_primary_keys) {
            $factory_panel = new FactoryPanel();
            $query = $factory_panel->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($factory_panel->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $factory_panel = $query->first();
            if (is_null($factory_panel)) {
                abort(404);
            }

            return $factory_panel;
        });

        Route::bind('factory_cycle_pattern', function ($joined_primary_keys) {
            $factory_cycle_pattern = new FactoryCyclePattern();
            $query = $factory_cycle_pattern->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($factory_cycle_pattern->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $factory_cycle_pattern = $query->first();
            if (is_null($factory_cycle_pattern)) {
                abort(404);
            }

            return $factory_cycle_pattern;
        });

        Route::bind('factory_species', function ($joined_primary_keys) {
            $factory_species = new FactorySpecies();
            $query = $factory_species->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($factory_species->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $factory_species = $query->first();
            if (is_null($factory_species)) {
                abort(404);
            }

            return $factory_species;
        });

        Route::bind('factory_product', function ($joined_primary_keys) {
            $factory_product = new FactoryProduct();
            $query = $factory_product->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($factory_product->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $factory_product = $query->first();
            if (is_null($factory_product)) {
                abort(404);
            }

            return $factory_product;
        });

        Route::bind('collection_time', function ($joined_primary_keys) {
            $collection_time = new CollectionTime();
            $query = $collection_time->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($collection_time->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $collection_time = $query->first();
            if (is_null($collection_time)) {
                abort(404);
            }

            return $collection_time;
        });

        Route::bind('growth_simulation', function ($joined_primary_keys) {
            $growth_simulation = new GrowthSimulation();
            $query = $growth_simulation->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($growth_simulation->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $growth_simulation = $query->first();
            if (is_null($growth_simulation)) {
                abort(404);
            }

            return $growth_simulation;
        });

        Route::bind('bed_state', function ($joined_primary_keys) {
            $bed_state = new BedState();
            $query = $bed_state->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($bed_state->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $bed_state = $query->first();
            if (is_null($bed_state)) {
                abort(404);
            }

            return $bed_state;
        });

        Route::bind('productized_result', function ($joined_primary_keys) {
            $productized_result = new ProductizedResult();
            $query = $productized_result->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($productized_result->getKeyName() as $idx => $key) {
                $query->where($key, $primary_keys[$idx]);
            }

            $productized_result = $query->first();
            if (is_null($productized_result)) {
                abort(404);
            }

            return $productized_result;
        });

        Route::bind('stocktaking', function ($joined_primary_keys) {
            $stocktaking = new Stocktaking();
            $query = $stocktaking->newQuery();

            $primary_keys = explode('|', $joined_primary_keys);
            foreach ($stocktaking->getKeyName() as $idx => $key) {
                $query->where($key, str_replace('\\', '/', $primary_keys[$idx]));
            }

            $stocktaking = $query->first();
            if (is_null($stocktaking)) {
                abort(404);
            }

            return $stocktaking;
        });

        Route::bind('simulation_date', function ($simulation_date) {
            try {
                $simulation_date = new SimulationDate($simulation_date);
            } catch (InvalidArgumentException $e) {
                abort(404);
            }

            return $simulation_date;
        });

        Route::bind('working_date', function ($working_date) {
            try {
                $working_date = new WorkingDate($working_date);
            } catch (InvalidArgumentException $e) {
                abort(404);
            }

            return $working_date;
        });

        Route::bind('harvesting_date', function ($harvesting_date) {
            try {
                $harvesting_date = new HarvestingDate($harvesting_date);
            } catch (InvalidArgumentException $e) {
                abort(404);
            }

            return $harvesting_date;
        });

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
