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
use App\Http\Requests\Master\CreateTransportCompanyRequest;
use App\Http\Requests\Master\SearchTransportCompaniesRequest;
use App\Http\Requests\Master\UpdateTransportCompanyRequest;
use App\Models\Master\TransportCompany;
use App\Services\Master\TransportCompanyService;

class TransportCompaniesController extends Controller
{
    /**
     * @var \App\Services\Master\TransportCompanyService
     */
    private $transport_company_service;

    /**
     * @param  \App\Services\Master\TransportCompanyService $transport_company_service
     * @return void
     */
    public function __construct(TransportCompanyService $transport_company_service)
    {
        parent::__construct();

        $this->transport_company_service = $transport_company_service;
    }

    /**
     * 運送会社マスタ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $transport_companies = [];

        $params = $request->session()->get('master.transport_companies.search', $request->old());
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            try {
                $transport_companies = $this->transport_company_service->searchTransportCompanies($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.transport_companies.index');
            }
        }

        return view('master.transport_companies.index')->with(compact('transport_companies', 'params'));
    }

    /**
     * 運送会社マスタ 検索
     *
     * @param  \App\Http\Requests\Master\SearchTransportCompaniesRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchTransportCompaniesRequest $request): RedirectResponse
    {
        $request->session()->put('master.transport_companies.search', $request->all());
        return redirect()->route('master.transport_companies.index');
    }

    /**
     * 運送会社マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.transport_companies.add');
    }

    /**
     * 運送会社マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateTransportCompanyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateTransportCompanyRequest $request): RedirectResponse
    {
        try {
            $transport_company = $this->transport_company_service->createTransportCompany($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.transport_companies.edit', $transport_company->transport_company_code)
            ->with([
                'alert' => $this->operations['success']
            ]);
    }

    /**
     * 運送会社マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, TransportCompany $transport_company): View
    {
        return view('master.transport_companies.edit')->with(compact('transport_company'));
    }

    /**
     * 運送会社マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateTransportCompanyRequest $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateTransportCompanyRequest $request,
        TransportCompany $transport_company
    ): RedirectResponse {
        try {
            $this->transport_company_service->updateTransportCompany($transport_company, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.transport_companies.edit', $transport_company->transport_company_code)
            ->with([
                'alert' => $this->operations['success']
            ]);
    }

    /**
     * 運送会社マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\TransportCompany
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, TransportCompany $transport_company): RedirectResponse
    {
        if (! $transport_company->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->transport_company_service->deleteTransportCompany($transport_company);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
