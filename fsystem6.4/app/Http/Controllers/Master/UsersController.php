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
use App\Http\Requests\Master\CreateUserRequest;
use App\Http\Requests\Master\SearchUsersRequest;
use App\Http\Requests\Master\UpdateUserRequest;
use App\Models\Master\User;
use App\Services\Master\UserService;

class UsersController extends Controller
{
    /**
     * @var \App\Services\Master\UserService
     */
    private $user_service;

    /**
     * @param  \App\Services\Master\UserService $user_service
     * @return void
     */
    public function __construct(UserService $user_service)
    {
        parent::__construct();

        $this->user_service = $user_service;
    }

    /**
     * ユーザマスタ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    */
    public function index(Request $request)
    {
        $users = [];

        $params = $request->session()->get('master.users.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $users = $this->user_service->searchUsers($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.users.index');
            }
        }

        return view('master.users.index')->with(compact('users', 'params'));
    }

    /**
     * ユーザマスタ検索
     *
     * @param  \App\Http\Requests\Master\SearchUsersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchUsersRequest $request): RedirectResponse
    {
        $request->session()->put('master.users.search', $request->all());
        return redirect()->route('master.users.index');
    }

    /**
     * ユーザマスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        $permissions = $this->user_service->getDefaultPermissions((int)old('affiliation'));
        return view('master.users.add')->with(compact('permissions', 'params'));
    }

    /**
     * ユーザマスタ 権限 再取得
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function permissions(Request $request): RedirectResponse
    {
        return redirect()->back()->withInput();
    }

    /**
     * ユーザマスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateUsersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->user_service->createUser($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.users.edit', $user->user_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * ユーザマスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\User $user
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, User $user): View
    {
        $permissions = $this->user_service->getCurrentPermissions($user);

        if ($affiliation = old('affiliation')) {
            $permissions = $this->user_service->getDefaultPermissions((int)$affiliation);
        }

        return view('master.users.edit')->with(compact('user', 'permissions', 'params'));
    }

    /**
     * ユーザマスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateCorporationRequest $request
     * @param  \App\Models\Master\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $this->user_service->updateUser($user, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.users.edit', $user->user_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * ユーザマスタ削除
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Master\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, User $user): RedirectResponse
    {
        try {
            $this->user_service->deleteUser($user);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * パスワードリセット
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request, User $user): RedirectResponse
    {
        try {
            $this->user_service->resetPassword($user);
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.users.edit', $user->user_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * API用 ユーザ検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function searchUsers(Request $request): array
    {
        if ($request->ajax()) {
            return $this->user_service->searchUserForSearchingApi($request->all());
        }

        abort(404);
    }
}
