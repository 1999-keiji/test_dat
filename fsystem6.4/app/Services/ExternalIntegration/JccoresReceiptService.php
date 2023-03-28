<?php 
namespace App\Services\ExternalIntegration;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Repositories\ExternalIntegration\JccoresDatReceiptRepository;
use PhpParser\Node\Expr\AssignOp\Concat;

class JccoresReceiptService
{
    public function __construct(
      JccoresDatReceiptRepository $jccores_dat_receipt_repo
      ){
      $this->jccores_dat_receipt_repo = $jccores_dat_receipt_repo;
    }
    public function dataGetReceipt($inputs){
        $productized_results = $this->jccores_dat_receipt_repo->getProductizeResult($inputs);
        $productized_result_details = $this->jccores_dat_receipt_repo->getProductizeResultDetails($inputs);
        $stock = $this->jccores_dat_receipt_repo->getStock($inputs);
        $stocktaking_details = $this->jccores_dat_receipt_repo->getStocktakingDetails($inputs);
        $data = $this->dataBeforeTriming($inputs, $productized_results);
        $data = array_merge($data, $this->dataAfterTriming($inputs, $productized_results));
        $data = array_merge($data, $this->dataWrapping($inputs, $stock));
        $data = array_merge($data, $this->dataDisposal($inputs, $productized_result_details));
        $data = array_merge($data, $this->dataWrappingProduct($inputs, $stocktaking_details));
        $data = array_merge($data, $this->dataWrappingProductDisposal($inputs, $stock));
        return $this->convertToString($data);
    }
    public function dataBeforeTriming($inputs, $productized_results){
      foreach($productized_results as $pr){
        if($pr['triming'] + $pr['product_failure'] +$pr['packing'] + $pr['crop_failure'] + $pr['sample'] != 0){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $pr['conversion_code'],
            'status' => 1060,
            'product_type' => $pr['species_code'],
            'reciept_type' => "C310",
            'company_code' => 300600,
            'partner_office' => null,
            'partner_cost_department' => null,
            'partner_product_type' => null,
            'production_quantity' => $pr['triming'] + $pr['product_failure'] +$pr['packing'] + $pr['crop_failure'] + $pr['sample'],
            'cost1' => null,
            'cost2' => null,
            'foreign_currency_unit' => null,
            'foreign_currency' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
        return $data;
    } 
    public function dataAfterTriming($inputs, $productized_results){
      foreach($productized_results as $pr){
        if($pr['weight_of_discarded'] != 0){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $pr['conversion_code'],
            'status' => 1070,
            'product_type' => $pr['species_code'],
            'reciept_type' => "C310",
            'company_code' => 300600,
            'partner_office' => null,
            'partner_cost_department' => null,
            'partner_product_type' => null,
            'production_quantity' => $pr['weight_of_discarded'],
            'cost1' => null,
            'cost2' => null,
            'foreign_currency_unit' => null,
            'foreign_currency' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
      return $data;
    } 
    public function dataWrapping($inputs, $stock){
      foreach($stock as $s){
        if($s['input_group'] == "J"){
          $status = 1090;
        }elseif($s['input_group'] == "H"){
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
          'office' => $s['conversion_code'],
          'status' => $status,
          'product_type' => $s['species_code']."-".floor($s['number_of_heads'])."-".$s['weight_per_number_of_heads']."-".$s['input_group'],
          'reciept_type' => "C310",
          'company_code' => 300600,
          'partner_office' => null,
          'partner_cost_department' => null,
          'partner_product_type' => null,
          'production_quantity' => $s['disposal_quantity'],
          'cost1' => null,
          'cost2' => null,
          'foreign_currency_unit' => null,
          'foreign_currency' => null,
          'expansion1' => null,
          'expansion2' => null,
          'expansion3' => null,
        );
      }
        return $data;
    } 
    public function dataDisposal($inputs, $productized_result_details){
      foreach($productized_result_details as $prd){
        if($prd['input_group'] == "H" && $prd['product_quantity'] != 0){
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $prd['conversion_code'],
            'status' => 2000,
            'product_type' => $prd['species_code']. "-". floor($prd['number_of_heads']). "-". $prd['weight_per_number_of_heads']. "-". $prd['input_group'],
            'reciept_type' => "C310",
            'company_code' => 300600,
            'partner_office' => null,
            'partner_cost_department' => null,
            'partner_product_type' => null,
            'production_quantity' => $prd['product_quantity'],
            'cost1' => null,
            'cost2' => null,
            'foreign_currency_unit' => null,
            'foreign_currency' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
      }
        return $data;
    } 
    public function dataWrappingProduct($inputs, $stocktaking_details){
      foreach($stocktaking_details as $sd){
        if($sd['input_group'] == "J"){
          $status = 1110;
        }elseif($sd['input_group'] == "H"){
          continue;
        }else{
          $status = 1100;
        }
        $data[] = array(
          'AP_type' => "A1",
          'data_species' => "J",
          'year' => substr($inputs['date_month'], 0,4),
          'year_month' => str_replace('-','',$inputs['date_month']),
          'account_unit' => 300600,
          'office' => $sd['conversion_code'],
          'status' => $status,
          'product_type' => $sd['species_code']."-".floor($sd['number_of_heads'])."-".$sd['weight_per_number_of_heads']."-".$sd['input_group'],
          'reciept_type' => "D100",
          'company_code' => 300600,
          'partner_office' => null,
          'partner_cost_department' => null,
          'partner_product_type' => null,
          'production_quantity' => $sd['stock_quantity'],
          'cost1' => null,
          'cost2' => null,
          'foreign_currency_unit' => null,
          'foreign_currency' => null,
          'expansion1' => null,
          'expansion2' => null,
          'expansion3' => null,
        );
      }
      return  $data;
    }
    public function dataWrappingProductDisposal($inputs, $stock){
      foreach($stock as $s){
        if($s['disposal_quantity'] != 0){
          if($s['input_group'] == "J"){
            $status = 1110;
          }elseif($s['input_group'] == "H"){
            continue;
          }else{
            $status = 1100;
          }
          $data[] = array(
            'AP_type' => "A1",
            'data_species' => "J",
            'year' => substr($inputs['date_month'], 0,4),
            'year_month' => str_replace('-','',$inputs['date_month']),
            'account_unit' => 300600,
            'office' => $s['conversion_code'],
            'status' => $status,
            'product_type' => $s['species_code']."-".floor($s['number_of_heads'])."-".$s['weight_per_number_of_heads']."-".$s['input_group'],
            'reciept_type' => "C310",
            'company_code' => 300600,
            'partner_office' => null,
            'partner_cost_department' => null,
            'partner_product_type' => null,
            'production_quantity' => $s['disposal_quantity'],
            'cost1' => null,
            'cost2' => null,
            'foreign_currency_unit' => null,
            'foreign_currency' => null,
            'expansion1' => null,
            'expansion2' => null,
            'expansion3' => null,
          );
        }
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