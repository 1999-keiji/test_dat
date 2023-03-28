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
use App\Models\Plan\PanelState;
use Carbon\Carbon;

class JccoresDatVolumeRepository{

  public function __construct(
    ProductizedResult $productized_results,
    ProductizedResultDetail $productized_result_details,
    Stock $stock,
    Order $order,
    StockState $stock_states
    ){
      $this->productized_results = $productized_results;
      $this->productized_result_details = $productized_result_details;
      $this->stock = $stock;
      $this->order = $order;
      $this->stock_states = $stock_states;
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
        sum(packing)packing, sum(crop_failure)crop_failure,
        sum(sample)sample, productized_results.factory_code, species_code,
        sum(weight_of_discarded)weight_of_discarded,
        conversion_code')
      ->join('factory_jccores', 'productized_results.factory_code', '=', 'factory_jccores.factory_code')
      ->groupBy('productized_results.factory_code', 'species_code')
      ->get();
  }
  public function getProductizeResultDetails($inputs){
    return $this->productized_result_details
        ->whereYear('productized_result_details.harvesting_date', substr($inputs['date_month'],0,4))
        ->whereMonth('productized_result_details.harvesting_date', substr($inputs['date_month'], -2))
        ->where(function($query) use($inputs){
          foreach($inputs['factories'] as $f){
            $query->orWhere('productized_result_details.factory_code', $f);
          }
        })
        ->whereNotIn('productized_result_details.input_group',["H"])
        ->selectRaw('productized_result_details.factory_code, productized_result_details.species_code, conversion_code, 
          sum(productized_result_details.number_of_heads * productized_result_details.product_quantity)by_stump,
          sum((productized_result_details.weight_per_number_of_heads * productized_result_details.product_quantity)/1000)product_weight')
        ->join('factory_jccores', 'productized_result_details.factory_code', '=', 'factory_jccores.factory_code')
        ->groupBy('productized_result_details.factory_code', 'productized_result_details.species_code')
        ->get();
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
      ->selectRaw('productized_result_details.factory_code, species_code, number_of_heads, weight_per_number_of_heads, input_group, sum(product_quantity)product_quantity, conversion_code')
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
  public function getOrder($inputs){
    return $this->order
      ->where('base_plus_delete_flag', 0)->whereYear('received_date', substr($inputs['date_month'],0,4))
      ->whereMonth('received_date', substr($inputs['date_month'], -2))
      ->where(function($query) use($inputs){
        foreach($inputs['factories'] as $f){
          $query->orWhere('orders.factory_code', $f);
        }
      })
      ->selectRaw('sum(order_quantity)order_quantity, product_code, orders.factory_code, received_date, conversion_code')
      ->join('factory_jccores', 'orders.factory_code', '=', 'factory_jccores.factory_code')
      ->groupBy('orders.factory_code', 'product_code')
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
      ->selectRaw('stock_states.factory_code, sum(stock_quantity)stock_quantity, species_code, number_of_heads, weight_per_number_of_heads, input_group, conversion_code')
      ->join('factory_jccores', 'stock_states.factory_code', '=', 'factory_jccores.factory_code')
      ->groupBy('stock_states.factory_code', 'species_code', 'number_of_heads', 'weight_per_number_of_heads', 'input_group')
      ->get();
      
  }
  public function getPanelState($inputs){
    $month = Carbon::createFromFormat('Y-m', $inputs['date_month']);
    for($i=1;$i<=$month->daysInMonth; $i++){
      $date = Carbon::createFromFormat('Y-m-d', $inputs['date_month'].'-'.sprintf('%02d',$i));
      $panel_data[] = PanelState::where(function($query) use($inputs){
        foreach($inputs['factories'] as $f){
          $query->orWhere('panel_state.factory_code', $f);
        }
      })
      ->where('current_growth_stage', 3)
      ->whereYear('next_growth_stage_date', substr($inputs['date_month'], 0, 4))
      ->whereMonth('next_growth_stage_date', substr($inputs['date_month'], -2))
      ->where('next_growth_stage_date' , $date->toDateString())
      ->where('date', $date->subDay()->toDateString())
      ->selectRaw('panel_state.factory_code, next_growth_stage_date, panel_state.factory_species_code, species_code, conversion_code,
        CASE WHEN using_hole_count = null THEN SUM(using_hole_count) ELSE SUM(number_of_holes) END AS harvesting_stump')
      ->join('factory_species', 'panel_state.factory_species_code', '=', 'factory_species.factory_species_code')
      ->join('factory_jccores', 'panel_state.factory_code', '=','factory_jccores.factory_code')
      ->groupBy('panel_state.factory_code', 'panel_state.factory_species_code', 'next_growth_stage_date')
      ->get();
    }
    // dd($panel_data);
    return $panel_data;
  }
}