<?php 
namespace App\Services\ExternalIntegration;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Repositories\ExternalIntegration\JccoresDatVolumeRepository;
use PhpParser\Node\Expr\AssignOp\Concat;

class JccoresVolumeService
{
    public function __construct(
      JccoresDatVolumeRepository $jccores_dat_volume_repo
      ){
      $this->jccores_dat_volume_repo = $jccores_dat_volume_repo;
    }
    public function dataGetVolume($inputs){
        $panel_state = $this->jccores_dat_volume_repo->getPanelState($inputs);
        $productized_results = $this->jccores_dat_volume_repo->getProductizeResult($inputs);
        $productized_result_details = $this->jccores_dat_volume_repo->getProductizeResultDetails($inputs);
        $stock = $this->jccores_dat_volume_repo->getStock($inputs);
        $productized_result_details_for_wrap = $this->jccores_dat_volume_repo->getProductizeresultForWrap($inputs);
        $stock_for_wrap = $this->jccores_dat_volume_repo->getStockForWrap($inputs);
        $stock_state = $this->jccores_dat_volume_repo->getStockState($inputs);
        $order = $this->jccores_dat_volume_repo->getOrder($inputs);
        $data = $this->dataBeforeTriming($inputs, $panel_state);
        $data = array_merge($data, $this->dataAfterTriming($inputs, $productized_results, $productized_result_details, $stock));
        $data = array_merge($data, $this->dataWrapping($inputs, $productized_result_details_for_wrap, $stock_for_wrap));
        $data = array_merge($data, $this->dataDisposal($inputs, $productized_result_details_for_wrap));
        $data = array_merge($data, $this->dataWrappingProduct($inputs, $stock_state));
        $data = array_merge($data, $this->dataProduct($inputs, $order));
        return $this->convertToString($data);
        
      }
    public function dataBeforeTriming($inputs, $panel_state){
      foreach($panel_state as $ps){
        if($ps->isEmpty()){
          continue;
        }
        foreach($ps as $p){
          $array[] = array(
            'conversion_code' => $p['conversion_code'],
            'species_code' => $p['species_code'],
            'harvesting_stump' => $p['harvesting_stump']
          );
        }
      }
        $groups = [];
        foreach ($array as $item) {
            $key = $item['conversion_code'];
            if (array_key_exists($key, $groups)) {
              // 工場が存在するとき
              if (array_key_exists($item['species_code'], $groups[$key])) {
                // 品種が存在するとき株数を合計する
                $groups[$key][$item['species_code']][] += $item['harvesting_stump'];
              }else{
                // 品種が存在しないとき、新しく品種と株数を作成する
                $groups[$key][$item['species_code']][] = $item['harvesting_stump'];
              }
            } else {
              // 工場が存在しないとき、工場と品種と株数を作成する
                $groups[$key][$item['species_code']][] = $item['harvesting_stump'];
            }
        }
        foreach($groups as $conversion=>$group){
          foreach($group as $species=>$g){
            $data[] = array(
              'AP_type' => "A1",
              'data_species' => "J",
              'year' => substr($inputs['date_month'], 0,4),
              'year_month' => str_replace('-','',$inputs['date_month']),
              'account_unit' => 300600,
              'office' => $conversion,
              'status' => 1060,
              'product_type' => $species,
              'production_quantity' => array_sum($g),
              'raised_quantity' => null,
              'expansion1' => null,
              'expansion2' => null,
              'expansion3' => null,
            );
          }
        }
        if(isset($data)){
            return $data;
          }else{
            return $data[]=null;
          }
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
            $data[] = array(
              'AP_type' => "A1",
              'data_species' => "J",
              'year' => substr($inputs['date_month'], 0,4),
              'year_month' => str_replace('-','',$inputs['date_month']),
              'account_unit' => 300600,
              'office' => $pr['conversion_code'],
              'status' => 1070,
              'product_type' => $pr['species_code'],
              'production_quantity' => $sum,
              'raised_quantity' => null,
              'expansion1' => null,
              'expansion2' => null,
              'expansion3' => null,
            );
          }
          if(isset($data)){
            return $data;
          }else{
            return $data[]=null;
          }
      } 
    public function dataWrapping($inputs, $productized_result_details_for_wrap, $stock_for_wrap){
        foreach($productized_result_details_for_wrap as $prdfw){
            foreach($stock_for_wrap as $sfw){
                if($prdfw['factory_code'] == $sfw['factory_code'] && $prdfw['species_code'] == $sfw['species_code'] && $prdfw['number_of_heads'] == $sfw['number_of_heads'] && $prdfw['weight_per_number_of_heads'] == $sfw['weight_per_number_of_heads'] && $prdfw['input_group'] == $sfw['input_group']){
                    if($prdfw['input_group'] == "J"){
                        $status = 1090;
                    }elseif($prdfw['input_group'] == "H"){
                        continue;
                    }else{
                        $status = 1080;
                    }
                    if($sfw['stock_quantity'] != 0){
                        $data[] = array(
                        'AP_type' => "A1",
                        'data_species' => "J",
                        'year' => substr($inputs['date_month'], 0,4),
                        'year_month' => str_replace('-','',$inputs['date_month']),
                        'account_unit' => 300600,
                        'office' => $prdfw['conversion_code'],
                        'status' => $status,
                        'product_type' => $prdfw['species_code']. "-". floor($prdfw['number_of_heads']). "-". $prdfw['weight_per_number_of_heads']. "-". $prdfw['input_group'],
                        'production_quantity' => $sfw['stock_quantity'],
                        'raised_quantity' => null,
                        'expansion1' => null,
                        'expansion2' => null,
                        'expansion3' => null,
                        );
                    }
                }
            }
        }
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
                'production_quantity' => $prdfw['product_quantity'],
                'raised_quantity' => null,
                'expansion1' => null,
                'expansion2' => null,
                'expansion3' => null,
              );
            }
        }
        if(isset($data)){
          return $data;
        }else{
          return $data[]=null;
        }
      } 
    public function dataWrappingProduct($inputs, $stock_state){
        foreach($stock_state as $ss){
            if($ss['input_group'] == "J"){
              $status = 1110;
            }elseif($ss['input_group'] == "H"){
                continue;
            }else{
              $status = 1100;
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
                'production_quantity' => $ss['stock_quantity'],
                'raised_quantity' => null,
                'expansion1' => null,
                'expansion2' => null,
                'expansion3' => null,
                );
            }
          }
          if(isset($data)){
            return $data;
          }else{
            return $data[]=null;
          }
      } 
    public function dataProduct($inputs, $order){
        foreach($order as $o){
            $data[] = array(
              'AP_type' => "A1",
              'data_species' => "J",
              'year' => substr($inputs['date_month'], 0,4),
              'year_month' => str_replace('-','',$inputs['date_month']),
              'account_unit' => 300600,
              'office' => $o['conversion_code'],
              'status' => "K-PRD",
              'product_type' => $o['product_code'],
              'production_quantity' => $o['order_quantity'],
              'raised_quantity' => null,
              'expansion1' => null,
              'expansion2' => null,
              'expansion3' => null,
            );
          }
          if(isset($data)){
            return $data;
          }else{
            return $data[]=null;
          }
      }
    public function convertToString($data){
        foreach($data as $d){
            $text_conversion = implode(',', $d);
            $text_conversion = $text_conversion."\n";
            $dat_data[] = $text_conversion;
        }
        dd(implode($dat_data));
        return implode($dat_data);
      }
}