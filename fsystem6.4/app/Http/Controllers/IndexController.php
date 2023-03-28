<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Master\MenuService;

class IndexController extends Controller
{
    /**
     * @var \App\Services\Master\MenuService
     */
    private $menu_service;

    /**
     * @param  \Illuminate\Hashing\BcryptHasher $hasher
     * @param  \App\Services\Master\MenuService $menu_service
     * @return void
     */
    public function __construct(MenuService $menu_service)
    {
        parent::__construct();

        $this->menu_service = $menu_service;
    }

    /**
     * メインメニュー
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $tabs = $this->menu_service->getAllTabs();
        return view('index')->with([
            'tabs' => $tabs,
            'active_tab' => $request->tab_code ?: $tabs->first()->tab_code
        ]);
    }

    /**
     * セッションのクリア
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear(Request $request): RedirectResponse
    {
        $tabs = $this->menu_service->getAllTabs()->pluck('tab_code')->all();
        $previous_tab = array_first(array_intersect(explode('/', url()->previous()), $tabs));

        $request->session()->forget($tabs);
        return redirect()->route('index', ['tab_code' => $previous_tab ?: '']);
    }
}
