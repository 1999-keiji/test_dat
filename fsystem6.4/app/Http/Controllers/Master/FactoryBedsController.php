<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\UpdateFactoryBedsRequest;
use App\Models\Master\Factory;
use App\Services\Master\FactoryService;

class FactoryBedsController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @return void
     */
    public function __construct(FactoryService $factory_service)
    {
        parent::__construct();

        $this->factory_service = $factory_service;
    }

    /**
     * 工場マスタ レイアウト
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        $factory_columns = $factory->factory_columns;
        $factory_beds = $factory->factory_beds->reverseRow();

        return view('master.factories.beds')->with(compact(
            'factory',
            'factory_columns',
            'factory_beds'
        ));
    }

    /**
     * 工場レイアウト更新
     *
     * @param  \App\Http\Requests\Master\UpdateFactoryBedsRequest
     * @param  \App\Models\Master\Factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateFactoryBedsRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_service->updateFactoryBeds($factory, $request->all());
        } catch (OptimisticLockException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
