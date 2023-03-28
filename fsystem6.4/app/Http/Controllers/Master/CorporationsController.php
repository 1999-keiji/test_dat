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
use App\Http\Requests\Master\CreateCorporationRequest;
use App\Http\Requests\Master\SearchCorporationsRequest;
use App\Http\Requests\Master\UpdateCorporationRequest;
use App\Services\Master\CorporationService;
use App\Models\Master\Corporation;
use App\Services\Master\FactoryService;

class CorporationsController extends Controller
{
    /**
     * @var \App\Services\Master\CorporationService
     */
    private $corporation_service;

    /**
     * @param  \App\Services\Master\CorporationService $corporation_service
     * @return void
     */
    public function __construct(CorporationService $corporation_service)
    {
        parent::__construct();

        $this->corporation_service = $corporation_service;
    }

    /**
     * 法人マスタ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $corporations = [];

        $params = $request->session()->get('master.corporations.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $corporations = $this->corporation_service->searchCorporations($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.corporations.index');
            }
        }

        return view('master.corporations.index')->with(compact('corporations', 'params'));
    }

    /**
     * 法人マスタ検索
     *
     * @param \App\Http\Requests\Master\SearchCorporationsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchCorporationsRequest $request): RedirectResponse
    {
        $request->session()->put('master.corporations.search', $request->all());
        return redirect()->route('master.corporations.index');
    }

    /**
     * 法人マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.corporations.add');
    }

    /**
     * 法人マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateCorporationsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateCorporationRequest $request): RedirectResponse
    {
        try {
            $corporation = $this->corporation_service->createCorporation($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.corporations.edit', $corporation->corporation_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 法人マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Corporation $corporation
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Corporation $corporation): View
    {
        return view('master.corporations.edit')->with(compact('corporation'));
    }

    /**
     * 法人マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateCorporationRequest $request
     * @param  \App\Models\Master\Corporation $corporation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCorporationRequest $request, Corporation $corporation): RedirectResponse
    {
        try {
            $this->corporation_service->updateCorporation($corporation, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.corporations.edit', $corporation->corporation_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 法人マスタ削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Corporation $corporation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Corporation $corporation): RedirectResponse
    {
        if (! $corporation->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->corporation_service->deleteCorporation($corporation);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
