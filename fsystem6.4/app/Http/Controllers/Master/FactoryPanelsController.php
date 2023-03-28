<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Master\Factory;
use App\Models\Master\FactoryPanel;
use App\Services\Master\FactoryPanelService;
use App\Http\Requests\Master\CreateFactoryPanelRequest;

class FactoryPanelsController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryPanelService
     */
    private $factory_panel_service;

    /**
     * @param  \App\Services\Master\FactoryPanelService $factory_panel_service
     * @return void
     */
    public function __construct(FactoryPanelService $factory_panel_service)
    {
        parent::__construct();

        $this->factory_panel_service = $factory_panel_service;
    }

    /**
     * 工場マスタ トレイ/パネル
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        $factory_panels = $factory->factory_panels->sortByNumberOfHolesDesc();
        return view('master.factories.panels')->with(compact(
            'factory',
            'factory_panels'
        ));
    }

    /**
     * 工場パネルマスタ パネル追加
     *
     * @param  \App\Http\Requests\Master\CreateFactoryPanelRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateFactoryPanelRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_panel_service->createFactoryPanel($factory, (int)$request->number_of_holes);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場倉庫マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryPanel $factory_panel
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Factory $factory, FactoryPanel $factory_panel): RedirectResponse
    {
        try {
            if (! $factory_panel->isDeletable()) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['forbidden']]);
            }

            $this->factory_panel_service->deleteFactoryPanel($factory_panel);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
