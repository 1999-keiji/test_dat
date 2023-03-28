<?php

namespace App\Services\ExternalIntegration;

use stdClass;
use InvalidArgumentException;
use Illuminate\Database\Connection;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Services\ExternalIntegration\JccoresVolumeService;
use App\Services\ExternalIntegration\JccoresReceiptService;
use App\Services\ExternalIntegration\JccoresConsumptionService;
use PhpParser\Node\Expr\AssignOp\Concat;

class JccoresService
{
    public function __construct(
      JccoresVolumeService $jccores_volume_service,
      JccoresReceiptService $jccores_receipt_service,
      JccoresConsumptionService $jccores_cunsumption_service
      ){
      $this->jccores_volume_service = $jccores_volume_service;
      $this->jccores_receipt_service = $jccores_receipt_service;
      $this->jccores_cunsumption_service = $jccores_cunsumption_service;
    }
    public function volume($inputs){
      return $this->jccores_volume_service->dataGetVolume($inputs);
    }
    public function receipt($inputs){
      return $this->jccores_receipt_service->dataGetReceipt($inputs);
    }
    public function consumption($inputs){
      return $this->jccores_cunsumption_service->dataGetConsumption($inputs);
    }
}
