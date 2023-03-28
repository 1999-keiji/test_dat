<?php 
namespace App\Services\ExternalIntegration;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Repositories\ExternalIntegration\JccoresDatConsumptionRepository;
use PhpParser\Node\Expr\AssignOp\Concat;

class JccoresConsumptionService
{
    public function __construct(
      JccoresDatConsumptionRepository $jccores_dat_consumption_repo
      ){
      $this->jccores_dat_consumption_repo = $jccores_dat_consumption_repo;
    }
    public function dataGetConsumption($inputs){
        $productized_results = $this->jccores_dat_consumption_repo->getProductizeResult($inputs);
        $productized_result_details = $this->jccores_dat_consumption_repo->getProductizeResultDetails($inputs);
        $stock = $this->jccores_dat_consumption_repo->getStock($inputs);
        $productized_result_details_for_wrap = $this->jccores_dat_consumption_repo->getProductizeresultForWrap($inputs);
        $stock_for_wrap = $this->jccores_dat_consumption_repo->getStockForWrap($inputs);
        $stock_states = $this->jccores_dat_consumption_repo->getStockState($inputs);
        $order = $this->jccores_dat_consumption_repo->getOrder($inputs);
        $factory_products = $this->jccores_dat_consumption_repo->getfactoryProduct($inputs);
        $order_product = $this->jccores_dat_consumption_repo->getOrderProduct($inputs);
        // dd("test", $products, $factory_products);
        $data = $this->dataBeforeTriming($inputs, $productized_results, $productized_result_details);
        $data = array_merge($data, $this->dataAfterTriming($inputs, $productized_results, $productized_result_details, $stock));
        $data = array_merge($data, $this->dataWrapping($inputs, $productized_result_details_for_wrap, $stock_for_wrap));
        $data = array_merge($data, $this->dataDisposal($inputs, $productized_result_details_for_wrap));
        $data = array_merge($data, $this->dataWrappingProduct($inputs, $stock_states));
        $data = array_merge($data, $this->dataProduct($inputs, $order_product));
        $data = array_merge($data, $this->dataProductDesposal($inputs, $order, $factory_products));
        return $this->convertToString($data);
        
    }
    public function dataBeforeTriming($inputs, $productized_results, $productized_result_details){
      foreach($productized_results as $pr){
        $sum = 0;
        foreach($productized_result_details as $prd){
          if($pr['factory_code'] == $prd['factory_code'] && $pr['species_code'] == $prd['species_code']){
            $sum = floor($pr['triming'] + $pr['product_failure'] + $pr['packing'] + $pr['crop_failure'] + $pr['sample'] + $prd['by_stump']);
          }
        }
        if($sum != 0){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $pr['conversion_code'],
            'status' => 1060,
            'product_type' => $pr['species_code'],
            'company_code' => 300600,
            'factory_code' => $pr['conversion_code'],
            'before_status' => 1050,
            'child_product_type' => $pr['species_code'],
            'production_quantity' => $sum,
            'unit_price' => null,
            'withdrawal_of_consumption' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
      return $data;
    } 
    public function dataAfterTriming($inputs, $productized_results, $productized_result_details, $stock){
      foreach($productized_results as $pr){
        $sum = 0;
        foreach($productized_result_details as $prd){
          if($pr['factory_code'] == $prd['factory_code'] && $pr['species_code'] == $prd['species_code']){
            $sum = floor($prd['product_weight'] + $pr['weight_of_discarded'] * 10) / 10;
          }
          foreach($stock as $s){
            if($pr['factory_code'] == $prd['factory_code'] && $pr['species_code'] == $prd['species_code']){
              $sum += floor($s['stock_weight'] * 10) / 10;
            }
          }
        }
        if($sum != 0){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $pr['conversion_code'],
            'status' => 1070,
            'product_type' => $pr['species_code'],
            'company_code' => 300600,
            'factory_code' => $pr['conversion_code'],
            'before_status' => 1060,
            'child_product_type' => $pr['species_code'],
            'production_quantity' => $sum,
            'unit_price' => null,
            'withdrawal_of_consumption' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
      return $data;
    } 
    public function dataWrapping($inputs, $productized_result_details_for_wrap, $stock_for_wrap){
      foreach($productized_result_details_for_wrap as $prdfw){
        foreach($stock_for_wrap as $sfw){
          if($prdfw['factory_code'] == $sfw['factory_code'] && $prdfw['species_code'] == $sfw['species_code'] && $prdfw['number_of_heads'] == $sfw['number_of_heads'] && $prdfw['weight_per_number_of_heads'] == $sfw['weight_per_number_of_heads'] && $prdfw['input_group'] == $sfw['input_group']){
            if($sfw['stock_quantity'] != 0){
              if($prdfw == "J"){
                $status = 1090;
              }elseif($sfw['input_group'] == "H"){
                continue;
              }else{
                $status = 1080;
              }
              $data[] = array(
                'AP_type' => "A1",
                'data_species' => "J",
                'year' => substr($inputs['date_month'], 0,4),
                'year_month' => str_replace('-','',$inputs['date_month']),
                'account_unit' => 300600,
                'office' => $prdfw['conversion_code'],
                'status' => $status,
                'product_type' => $prdfw['species_code']. "-". floor($prdfw['number_of_heads']). "-". $prdfw['weight_per_number_of_heads']. "-". $prdfw['input_group'],
                'company_code' => 300600,
                'factory_code' => $prdfw['conversion_code'],
                'before_status' => 1070,
                'child_product_type' => $prdfw['species_code'],
                'production_quantity' => $sfw['stock_quantity'],
                'unit_price' => null,
                'withdrawal_of_consumption' => null,
                'expansion1' => null,
                'expansion2' => null,
                'expansion3' => null,
              );
            }
          }
        }
      }
        return $data;
    } 
    public function dataDisposal($inputs, $productized_result_details_for_wrap){
      foreach($productized_result_details_for_wrap as $prdfw){
        if($prdfw['input_group'] == "H"){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $prdfw['conversion_code'],
            'status' => 2000,
            'product_type' => $prdfw['species_code']. "-". floor($prdfw['number_of_heads']). "-". $prdfw['weight_per_number_of_heads']. "-". $prdfw['input_group'],
            'company_code' => 300600,
            'factory_code' => $prdfw['conversion_code'],
            'before_status' => 1070,
            'child_product_type' => $prdfw['species_code'],
            'production_quantity' => $prdfw['product_quantity'],
            'unit_price' => null,
            'withdrawal_of_consumption' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
        return $data;
    } 
    public function dataWrappingProduct($inputs, $stock_states){
      foreach($stock_states as $ss){
        if($ss['input_group'] == "J"){
         $status = 1110;
         $before_status = 1090;
        }elseif($ss['input_group'] == "H"){
          continue;
        }else{
         $status = 1100;
         $before_status = 1080;
        }
        if($ss['stock_quantity'] != 0){
          $data[] = array(
           'AP_type' => "A1",
           'data_species' => "J",
           'year' => substr($inputs['date_month'], 0,4),
           'year_month' => str_replace('-','',$inputs['date_month']),
           'account_unit' => 300600,
           'office' => $ss['conversion_code'],
           'status' => $status,
           'product_type' => $ss['species_code']. "-". floor($ss['number_of_heads']). "-". $ss['weight_per_number_of_heads']. "-". $ss['input_group'],
           'company_code' => 300600,
           'factory_code' => $ss['conversion_code'],
           'before_status' => $before_status,
           'child_product_type' => $ss['species_code']. "-". floor($ss['number_of_heads']). "-". $ss['weight_per_number_of_heads']. "-". $ss['input_group'],
           'production_quantity' => $ss['stock_quantity'],
           'unit_price' => null,
           'withdrawal_of_consumption' => null,
           'expansion1' => null,
           'expansion2' => null,
           'expansion3' => null,
          );
        }
      }
      return  $data;
    } 
    public function dataProduct($inputs, $order_product){
      foreach($order_product as $op){
        if($op['input_group'] == "J"){
          $before_status = 1110;
        }elseif($op['input_group'] == "H"){
          continue;
        }else{
          $before_status = 1100;
        }
        $data[] = array(
          'AP_type' => "A1",
          'data_species' => "J",
          'year' => substr($inputs['date_month'], 0,4),
          'year_month' => str_replace('-','',$inputs['date_month']),
          'account_unit' => 300600,
          'office' => $op['conversion_code'],
          'status' => "K-PRD",
          'product_type' => $op['product_code'],
          'company_code' => 300600,
          'factory_code' => $op['conversion_code'],
          'before_status' => $before_status,
          'child_product_type' => $op['species_code']. "-". floor($op['number_of_heads']). "-". $op['weight_per_number_of_heads']. "-". $op['input_group'],
          'production_quantity' => $op['order_quantity'],
          'unit_price' => null,
          'withdrawal_of_consumption' => null,
          'expansion1' => null,
          'expansion2' => null,
          'expansion3' => null,
        );
      }
      return $data;
    }
    public function dataProductDesposal($inputs, $order, $factory_products){
      foreach($order as $o){
          foreach($factory_products as $fp){
            if($o['product_code'] == $fp['product_code'] && $fp['inpu_group'] != "H"){
              $species = $o['species_code']."-".floor($fp['number_of_heads'])."-".$fp['weight_per_number_of_heads']."-".$fp['input_group'];
            }
          }
        $data[] = array(
          'AP_type' => "A1",
          'data_species' => "J",
          'year' => substr($inputs['date_month'], 0,4),
          'year_month' => str_replace('-','',$inputs['date_month']),
          'account_unit' => 300600,
          'office' => $o['conversion_code'],
          'status' => "K-PRD",
          'product_type' => $o['product_code'],
          'company_code' => 300600,
          'factory_code' => $o['conversion_code'],
          'before_status' => 1100,
          'child_product_type' => $species,
          'production_quantity' => $o['order_quantity'],
          'unit_price' => null,
          'withdrawal_of_consumption' => null,
          'expansion1' => null,
          'expansion2' => null,
          'expansion3' => null,
        );
      } 
      return $data;
    }
    public function convertToString($data){
        foreach($data as $d){
            $text_conversion = implode(',', $d);
            $text_conversion = $text_conversion."\n";
            $dat_data[] = $text_conversion;
        }
        return implode($dat_data);
    }
}