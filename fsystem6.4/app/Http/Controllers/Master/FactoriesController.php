<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateFactoryRequest;
use App\Http\Requests\Master\SearchFactoriesRequest;
use App\Http\Requests\Master\UpdateFactoryRequest;
use App\Models\Master\Factory;
use App\Services\Master\FactoryService;
use App\Services\Master\WarehouseService;

class FactoriesController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Master\WarehouseService $warehouse_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        WarehouseService $warehouse_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->warehouse_service = $warehouse_service;
    }

    /**
     * 工場マスタ 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $factories = [];

        $params = $request->session()->get('master.factories.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            try {
                $factories = $this->factory_service->searchFactories($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.factories.index');
            }
        }

        return view('master.factories.index')->with(compact(
            'factories',
            'corporation_list',
            'params'
        ));
    }

    /**
     * 工場マスタ 検索
     *
     * @param  \App\Http\Requests\Master\SearchFactoriesRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchFactoriesRequest $request): RedirectResponse
    {
        $request->session()->put('master.factories.search', $request->all());
        return redirect()->route('master.factories.index');
    }

    /**
     * 工場マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.factories.add');
    }

    /**
     * 工場マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateFactoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateFactoryRequest $request): RedirectResponse
    {
        try {
            $factory = $this->factory_service->createFactory($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.factories.edit', $factory->factory_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 工場マスタ 修正 基本情報
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Factory $factory): View
    {
        $factory->overwrite_on_invoice = $factory->willOverwriteOnInvoice();
        return view('master.factories.base_data')->with(compact('factory'));
    }

    /**
     * 工場マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateFactoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateFactoryRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_service->updateFactory($factory, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.factories.edit', $factory->factory_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 工場マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Factory $factory): RedirectResponse
    {
        if (! $factory->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }
        try {
            $this->factory_service->deleteFactory($factory);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
