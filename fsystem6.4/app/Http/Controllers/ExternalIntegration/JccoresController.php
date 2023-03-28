<?php

namespace App\Http\Controllers\ExternalIntegration;

use InvalidArgumentException;
use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Master\Factory;
use App\Models\Master\FactoryJccores;
use App\Models\Master\FactoryProduct;
use App\Models\Master\Product;
use App\Models\Order\Order;
use App\Models\Shipment\ProductAllocation;
use App\Models\Shipment\ProductizedResult;
use App\Models\Shipment\ProductizedResultDetail;
use App\Models\Stock\Stock;
use App\Models\Stock\StockState;
use Illuminate\Http\File;
use Illuminate\Support\Facades\File as FacadesFile;
use App\Http\Requests\ExternalIntegration\SearchJccoresRequest;
use App\Models\Plan\PanelState;
use App\Models\Stock\StocktakingDetail;
use App\Services\ExternalIntegration\JccoresService;
use ZipArchive;
use App\Services\Shipment\ProductizedResultService;

class JccoresController extends Controller
{
    public function __construct(
      JccoresService $jccores_service,
      Factory $factory
    )
    {
      $this->jccores_service = $jccores_service;
      $this->factories = $factory;
    }

    public function index(Request $request){
      $factories = $this->factories->get();
      $params = $request->session()->get('external_integration.jccores.search', []);
      return view('external_integration.index')->with(compact('factories', 'params'));
    }
    public function search(SearchJccoresRequest $request): RedirectResponse{
      $request->session()->put('external_integration.jccores.search', $request->all());
      return redirect()->route('external_integration.jccores.index');
    }
    // 生産量ファイル出力
    public function volume(Request $request){
      $inputs = $request->session()->get('external_integration.jccores.search', []);
      // header('Content-Type: text/plain');
      // header('Content-Disposition: attachment; filename="FGSIA1FS.dat"');
      return $this->jccores_service->volume($inputs);
    }
    // 受払ファイル出力
    public function receipt(Request $request){
      $inputs = $request->session()->get('external_integration.jccores.search', []);
      header('Content-Type: text/plain');
      header('Content-Disposition: attachment; filename="FGSIF1FS.dat"');
      return $this->jccores_service->receipt($inputs);
    }
    // 消費量ファイル出力
    public function consumption(Request $request){
      $inputs = $request->session()->get('external_integration.jccores.search', []);
      header('Content-Type: text/plain');
      header('Content-Disposition: attachment; filename="FGSIA2FS.dat"');
      return $this->jccores_service->consumption($inputs);
    }
    // 上記３つのファイルをzipの中に入れる
    public function zip(Request $request){
      
    }
}
