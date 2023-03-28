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
use App\Models\Stock\StocktakingDetail;

class JccoresDatReceiptRepository{
    public function __construct(
      ProductizedResult $productized_results,
      ProductizedResultDetail $productized_result_details,
      Stock $stock,
      StocktakingDetail $stocktaking_details
    ){
      $this->productized_results = $productized_results;
      $this->productized_result_details = $productized_result_details;
      $this->stock = $stock;
      $this->stocktaking_details = $stocktaking_details;
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
        ->selectRaw('sum(triming)triming, sum(product_failure)product_failure,
          sum(packing)packing, sum(crop_failure)crop_failure, sum(sample)sample,
          productized_results.factory_code, species_code, conversion_code,
          sum(weight_of_discarded)weight_of_discarded')
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
        ->selectRaw('sum(number_of_heads * product_quantity)product_weight,
          productized_result_details.factory_code, species_code, conversion_code,
          number_of_heads, weight_per_number_of_heads, input_group,
          sum(product_quantity)product_quantity')
          ->join('factory_jccores', 'productized_result_details.factory_code', '=', 'factory_jccores.factory_code')
        ->groupBy('productized_result_details.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
        ->get();
    }
    public function getStock($inputs) {
      return $this->stock
        ->whereYear('harvesting_date', substr($inputs['date_month'],0,4))
        ->whereMonth('harvesting_date', substr($inputs['date_month'], -2))
        ->where(function($query) use($inputs){
          foreach($inputs['factories'] as $f){
            $query->orWhere('stocks.factory_code', $f);
          }
        })
        ->where('order_number', null)->where('stock_weight', '!=' ,0)
        ->selectRaw('sum(disposal_quantity)disposal_quantity,
          stocks.factory_code, species_code, number_of_heads, conversion_code,
          weight_per_number_of_heads, input_group')
          ->join('factory_jccores', 'stocks.factory_code', '=', 'factory_jccores.factory_code')
        ->groupBy('stocks.factory_code', 'species_code')
        ->get();
    }
    public function getStocktakingDetails($inputs){
      return $this->stocktaking_details
        ->where('stocktaking_month', str_replace('-','/',$inputs['date_month']))
        ->where(function($query) use($inputs){
          foreach($inputs['factories'] as $f){
            $query->orWhere('stocktaking_details.factory_code', $f);
          }
        })
        ->selectRaw('stocktaking_details.factory_code, stocktaking_month, species_code, conversion_code,
          number_of_heads, weight_per_number_of_heads, input_group,
          sum(stock_quantity)stock_quantity')
        ->join('factory_jccores', 'stocktaking_details.factory_code', '=', 'factory_jccores.factory_code')
        ->groupBy('stocktaking_details.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
        ->get();
    }
}