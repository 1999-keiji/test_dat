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
use App\Http\Requests\Master\CreateEndUserRequest;
use App\Http\Requests\Master\CreateEndUserFactoryRequest;
use App\Http\Requests\Master\SearchEndUsersRequest;
use App\Http\Requests\Master\UpdateEndUserRequest;
use App\Models\Master\EndUser;
use App\Models\Master\EndUserFactory;
use App\Services\Master\EndUserService;
use App\Services\Master\FactoryService;

class EndUsersController extends Controller
{
    /**
     * @var \App\Services\Master\EndUserService
     */
    private $end_user_service;

    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @param \App\Services\Master\EndUserService $end_user_service
     * @param \App\Services\Master\FactoryService $factory_service
     */
    public function __construct(EndUserService $end_user_service, FactoryService $factory_service)
    {
        parent::__construct();

        $this->end_user_service = $end_user_service;
        $this->factory_service = $factory_service;
    }

    /**
     * エンドユーザ 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $end_users = [];

        $params = $request->session()->get('master.end_users.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $end_users = $this->end_user_service->searchEndUsers($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.end_users.index');
            }
        }

        return view('master.end_users.index')->with(compact('end_users', 'params'));
    }

    /**
     * エンドユーザ 検索
     *
     * @param  \App\Http\Requests\Master\SearchProductsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchEndUsersRequest $request): RedirectResponse
    {
        $request->session()->put('master.end_users.search', $request->all());
        return redirect()->route('master.end_users.index');
    }

    /**
     * エンドユーザ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.end_users.add');
    }

    /**
     * エンドユーザ 登録
     *
     * @param  \App\Http\Requests\Master\CreateEndUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateEndUserRequest $request): RedirectResponse
    {
        try {
            $end_user = $this->end_user_service->createEndUser($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.end_users.edit', $end_user->getJoinedPrimaryKeys())->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * エンドユーザ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\EndUser $end_user
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, EndUser $end_user): View
    {
        return view('master.end_users.edit')->with(compact('end_user'));
    }

    /**
     * エンドユーザ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateEndUserRequest $request
     * @param  \App\Models\Master\EndUser $end_user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateEndUserRequest $request, EndUser $end_user): RedirectResponse
    {
        try {
            $this->end_user_service->updateEndUser($end_user, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.end_users.edit', $end_user->getJoinedPrimaryKeys())->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * エンドユーザ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\EndUser $end_user
     * @return Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, $end_user): RedirectResponse
    {
        if (! $end_user->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->end_user_service->deleteEndUser($end_user);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * エンドユーザ工場 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\EndUser $end_user
     * @return \Illuminate\View\View
     */
    public function factories(Request $request, EndUser $end_user)
    {
        $factories = $this->factory_service->getNotLinkedEndUserFactories($end_user);
        return view('master.end_users.factories')->with(compact('end_user', 'factories'));
    }

    /**
     * エンドユーザ工場 登録
     *
     * @param  \App\Http\Requests\Master\CreateEndUserFactoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFactory(CreateEndUserFactoryRequest $request): RedirectResponse
    {
        try {
            $this->end_user_service->createEndUserFactory($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * エンドユーザ工場 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\EndUserFactory $end_user_factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFactory(Request $request, EndUserFactory $end_user_factory): RedirectResponse
    {
        try {
            $this->end_user_service->deleteEndUserFactory($end_user_factory);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 エンドユーザ検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function searchEndUsers(Request $request): array
    {
        if ($request->ajax()) {
            return $this->end_user_service->searchEndUsersForSearchingApi($request->all());
        }

        abort(404);
    }
}
