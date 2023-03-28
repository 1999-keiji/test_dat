<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Services\Master\UserService;

class AuthController extends Controller
{
    /**
     * @var \Illuminate\Auth\AuthManager
     */
    private $auth;

    /**
     * @var \Illuminate\Hashing\BcryptHasher
     */
    private $hasher;

    /**
     * @var \App\Services\Master\UserService
     */
    private $user_service;

    /**
     * @param  \Illuminate\Auth\AuthManager $auth
     * @param  \Illuminate\Hashing\BcryptHasher $hasher
     * @param  \App\Services\Master\UserService $user_service
     * @return void
     */
    public function __construct(AuthManager $auth, BcryptHasher $hasher, UserService $user_service)
    {
        parent::__construct();

        $this->auth = $auth;
        $this->hasher = $hasher;
        $this->user_service = $user_service;
    }

    /**
     * ログイン
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        return view('auth.index');
    }

    /**
     * ログイン試行
     *
     * @param  \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        if ($this->auth->attempt($request->only('user_code', 'password'), false)) {
            return redirect()->intended(route('index'));
        }

        return redirect()->back()->with('alert', $this->operations['fail']);
    }

    /**
     * ログアウト
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->auth->logout();

        $request->session()->flush();
        return redirect()->route('auth.index');
    }

    /**
     * パスワード変更
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function password(Request $request): View
    {
        $user = $this->auth->user();
        return view('auth.password')->with(compact('user'));
    }

    /**
     * パスワード変更 実行
     *
     * @param  \App\Http\Requests\ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $this->auth->user();
        if (! $this->hasher->check($request->current_password, $user->password)) {
            return redirect()->back()->with(['alert' => $this->operations['not_matched']]);
        }

        try {
            $this->user_service->resetPassword($user, $request->password);
        } catch (PDOException $e) {
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
