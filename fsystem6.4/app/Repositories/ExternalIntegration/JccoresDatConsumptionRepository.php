<?php

declare(strict_types=1);

namespace App\Repositories\ExternalIntegration;

use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Models\Order\Order;
use App\Models\Shipment\ProductizedResult;
use App\Models\Shipment\ProductizedResultDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockState;
use App\Models\Master\FactoryJccores;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Product;

class JccoresDatConsumptionRepository{
  public function __construct(
    ProductizedResult $productized_results,
    ProductizedResultDetail $productized_result_details,
    Stock $stock,
    Order $order,
    StockState $stock_states,
    Product $products,
    FactoryProduct $factory_products
  ){
    $this->productized_results = $productized_results;
    $this->productized_result_details = $productized_result_details;
    $this->stock = $stock;
    $this->order = $order;
    $this->stock_states = $stock_states;
    $this->products = $products;
    $this->factory_products = $factory_products;
  }
  public function getProductizeResult($inputs){
    return $this->productized_results
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('productized_results.factory_code', $f);
      }
    })
    ->selectRaw('sum(triming)triming, sum(product_failure), sum(packing)packing, sum(crop_failure)crop_failure,
      sum(sample)sample, productized_results.factory_code, species_code, sum(weight_of_discarded)weight_of_discarded, conversion_code')
    ->join('factory_jccores', 'productized_results.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('productized_results.factory_code', 'species_code')->get();
  }
  public function getProductizeResultDetails($inputs){
    return $this->productized_result_details
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('productized_result_details.factory_code', $f);
      }
    })
    ->selectRaw('sum(number_of_heads * product_quantity)by_stump, productized_result_details.factory_code, 
      species_code, sum((weight_per_number_of_heads * product_quantity)/1000)product_weight, conversion_code')
    ->join('factory_jccores', 'productized_result_details.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('productized_result_details.factory_code', 'species_code')->get();
  }
  public function getStock($inputs){
    return $this->stock
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('stocks.factory_code', $f);
      }
    })
    ->where('order_number', null)->where('stock_weight', '!=' ,0)
    ->selectRaw('sum(stock_weight)stock_weight, stocks.factory_code, species_code, conversion_code')
    ->join('factory_jccores', 'stocks.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('stocks.factory_code', 'species_code')
    ->get();
  }
  public function getProductizeresultForWrap($inputs){
    return $this->productized_result_details
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('productized_result_details.factory_code', $f);
      }
    })
    ->selectRaw('productized_result_details.factory_code, species_code, number_of_heads, weight_per_number_of_heads,
     input_group, sum(product_quantity)product_quantity, conversion_code')
    ->join('factory_jccores', 'productized_result_details.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('productized_result_details.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
    ->get();
  }
  public function getStockForWrap($inputs){
    return $this->stock
      ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
      ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
      ->where(function($query) use($inputs){
        foreach($inputs['factories'] as $f){
          $query->orWhere('stocks.factory_code', $f);
        }
      })
      ->where('order_number', null)
      ->selectRaw('stocks.factory_code, species_code, number_of_heads, weight_per_number_of_heads, input_group, sum(stock_quantity)stock_quantity, conversion_code')
      ->join('factory_jccores', 'stocks.factory_code', '=', 'factory_jccores.factory_code')
      ->groupBy('stocks.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
      ->get();
  }
  public function getStockState($inputs){
    return $this->stock_states
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('stock_states.factory_code', $f);
      }
    })
    ->selectRaw('stock_states.factory_code, sum(stock_quantity)stock_quantity, species_code, number_of_heads,
     weight_per_number_of_heads, input_group, conversion_code')
    ->join('factory_jccores', 'stock_states.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('stock_states.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
    ->get();
  }
  public function getOrder($inputs){
    return $this->order
      ->where('orders.base_plus_delete_flag', 0)
      ->whereYear('received_date', substr($inputs['date_month'],0,4))
      ->whereMonth('received_date', substr($inputs['date_month'], -2))
      ->where(function($query) use($inputs){
        foreach($inputs['factories'] as $f){
          $query->orWhere('orders.factory_code', $f);
        }
      })
      ->selectRaw('sum(order_quantity)order_quantity, orders.product_code, orders.factory_code, received_date, conversion_code, species_code')
      ->join('products', 'orders.product_code', '=', 'products.product_code')
      // ->join('factory_products', function ($q){
      //   $q->on('orders.product_code', '=', 'factory_products.product_code')
      //     ->where('orders.factory_code', '=', 'factory_products.factory_code');
      //   })
      ->join('factory_jccores', 'orders.factory_code', '=', 'factory_jccores.factory_code')
      ->groupBy('orders.factory_code', 'orders.product_code')
      ->get();
  }
  public function getOrderProduct($inputs){
    return $this->order
    ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
    ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('stocks.factory_code', $f);
      }
    })
    ->selectRaw('orders.order_number, product_code, sum(order_quantity)order_quantity,
      orders.factory_code, stocks.input_group, stocks.species_code, number_of_heads,
      weight_per_number_of_heads, conversion_code')
    ->join('stocks', 'orders.order_number', '=', 'stocks.order_number')
    ->join('factory_jccores', 'orders.factory_code', '=', 'factory_jccores.factory_code')
    ->groupBy('orders.factory_code', 'product_code')
    ->get();
  }
  public function getFactoryProduct($inputs){
    return $this->factory_products
    ->where(function($query) use($inputs){
      foreach($inputs['factories'] as $f){
        $query->orWhere('factory_code', $f);
      }
    })
    ->get();
  }
}