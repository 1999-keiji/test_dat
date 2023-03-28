<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Models\Master\Factory;
use App\Models\Master\FactoryJccores;
use App\Services\Master\FactoryService;
use App\Services\Master\FactoryJccoresService as MasterFactoryJccoresService;

class FactoryJccoresController extends Controller
{
    
    // public function __construct(MasterFactoryJccoresService $factory_jccores_service)
    // {
    //     parent::__construct();
    //     $this->factory_jccores_service = $factory_jccores_service;
    // }

    public function index(Request $request, Factory $factory): View {
        return view('master.factories.jccores')->with(compact('factory'));
    }

    public function update(Request $request, Factory $factory){
        dd($request->all());
        // try {
        //     $this->factory_service->updateFactoryjccores($factory, $request->all());
        // } catch (OptimisticLockException $e) {
        //     report($e);
        //     return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        // } catch (PDOException $e) {
        //     report($e);
        //     return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        // }
    }
}
