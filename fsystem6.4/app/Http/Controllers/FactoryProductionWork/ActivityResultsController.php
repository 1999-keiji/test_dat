<?php

declare(strict_types=1);

namespace App\Http\Controllers\FactoryProductionWork;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\FactoryProductionWork\SearchActivityResultsRequest;
use App\Http\Requests\FactoryProductionWork\SearchPanelActivityResultsRequest;
use App\Http\Requests\FactoryProductionWork\UpdateActivityResultsRequest;
use App\Models\Master\FactorySpecies;
use App\Services\FactoryProductionWork\ActivityResultsService;

class ActivityResultsController extends Controller
{
    /**
     * @var \App\Services\FactoryProductionWork\ActivityResultsService
     */
    private $activity_results_service;

    /**
     * @param  \App\Services\FactoryProductionWork\ActivityResultsService $activity_results_service
     * @return void
     */
    public function __construct(
        ActivityResultsService $activity_results_service
    ) {
        parent::__construct();

        $this->activity_results_service = $activity_results_service;
    }

    /**
     * 活動実績 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $activity_results = [];
        $params = $request->session()->get('factory_production_work.activity_results.search', []);
        if (count($params) !== 0) {
            $activity_results = $this->activity_results_service->getActivityResults($params);
        }

        return view('factory_production_work.activity_results.index')->with(compact('activity_results', 'params'));
    }

    /**
     * 活動実績 検索
     *
     * @param  \App\Http\Requests\FactoryProductionWork\SearchActivityResultsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchActivityResultsRequest $request): RedirectResponse
    {
        $request->session()->put('factory_production_work.activity_results.search', $request->all());
        return redirect()->route('factory_production_work.activity_results.index');
    }

    /**
     * 活動実績 入力
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, FactorySpecies $factory_species): View
    {
        $params = $request->session()->get('factory_production_work.activity_results.search', []);

        $panel_params = $request->session()->get('factory_production_work.activity_results.panel_search', []);
        $panels = count($panel_params) !== 0 ?
            $this->activity_results_service->getActivityPanels($factory_species, $panel_params) : [];

        return view('factory_production_work.activity_results.edit')
            ->with(compact('params', 'factory_species', 'panel_params', 'panels'));
    }

    /**
     * 活動実績入力 検索
     *
     * @param  \App\Http\Requests\FactoryProductionWork\SearchPanelActivityResultsRequest
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function panelSearch(
        SearchPanelActivityResultsRequest $request,
        FactorySpecies $factory_species
    ): RedirectResponse {
        $params = $request->all();
        if (count($params) === 0) {
            $request->session()->forget('factory_production_work.activity_results.panel_search');
            return redirect()->route('factory_production_work.activity_results.index');
        }

        $request->session()->put('factory_production_work.activity_results.panel_search', $params);
        return redirect()
            ->route('factory_production_work.activity_results.edit', $factory_species->getJoinedPrimaryKeys());
    }

    /**
     * 活動実績入力 更新
     *
     * @param  \App\Http\Requests\FactoryProductionWork\UpdateActivityResultsRequest
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateActivityResultsRequest $request, FactorySpecies $factory_species): RedirectResponse
    {
        try {
            $this->activity_results_service->update($factory_species, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('factory_production_work.activity_results.edit', $factory_species->getJoinedPrimaryKeys())
            ->with([
                'alert' => $this->operations['success']
            ]);
    }
}
