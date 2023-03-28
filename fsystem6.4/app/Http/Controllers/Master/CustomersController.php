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
use App\Http\Requests\Master\CreateCustomerRequest;
use App\Http\Requests\Master\SearchCustomersRequest;
use App\Http\Requests\Master\UpdateCustomerRequest;
use App\Models\Master\Customer;
use App\Services\Master\CustomerService;

class CustomersController extends Controller
{
    /**
     * @var \App\Services\Master\CustomerService
     */
    private $customer_service;

    /**
     * @param  \App\Services\Master\CustomerService $customer_service
     * @return void
     */
    public function __construct(CustomerService $customer_service)
    {
        parent::__construct();

        $this->customer_service = $customer_service;
    }

    /**
     * 得意先マスタ 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // 得意先一覧情報
        $customers = [];

        // 検索パラメータ取得
        $params = $request->session()->get('master.customers.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            try {
                $customers = $this->customer_service->searchCustomers($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.customers.index');
            }
        }

        return view('master.customers.index')->with(compact('customers', 'params'));
    }

    /**
     * 得意先マスタ 検索
     *
     * @param  \App\Http\Requests\Master\SearchCustomersRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchCustomersRequest $request): RedirectResponse
    {
        // リクエスト情報をセッションに追加
        $request->session()->put('master.customers.search', $request->all());
        // 一覧表示
        return redirect()->route('master.customers.index');
    }

    /**
     * 得意先マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.customers.add');
    }

    /**
     * 得意先マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateCustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateCustomerRequest $request): RedirectResponse
    {
        try {
            $customer = $this->customer_service->createCustomer($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.customers.edit', $customer->customer_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 得意先マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Customer $customer
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Customer $customer): View
    {
        return view('master.customers.edit')->with(compact('customer'));
    }

    /**
     * 得意先マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateCustomerRequest $request
     * @param  \App\Models\Master\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $this->customer_service->updateCustomer($customer, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.customers.edit', $customer->customer_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 得意先マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Customer $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Customer $customer): RedirectResponse
    {
        if (! $customer->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->customer_service->deleteCustomer($customer);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
