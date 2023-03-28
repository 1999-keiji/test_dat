<?php

namespace App\Http\Controllers\Plan;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\PageOverException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreateBedStateRequest;
use App\Http\Requests\Plan\SearchBedStatesRequest;
use App\Models\Plan\BedState;
use App\Services\Master\FactoryService;
use App\Services\Plan\BedStateService;
use App\ValueObjects\Date\WorkingDate;

class BedStatesController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Plan\BedStateService
     */
    private $bed_state_service;

    /**
     * @param  \App\Services\Master\FactoryService $factory_service
     * @param  \App\Services\Plan\BedStateService $bed_state_service
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        BedStateService $bed_state_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->bed_state_service = $bed_state_service;
    }

    /**
     * ベッド状況確認 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $factories = $this->factory_service->getAllFactories();
        $bed_states = [];

        $params = $request->session()->get('plan.bed_states.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            $order = $request->only(['sort', 'order']);

            try {
                $bed_states = $this->bed_state_service->searchBedStates($params, (int)$page, $order);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('plan.bed_states.index');
            }
        }

        return view('plan.bed_states.index')->with(compact('factories', 'bed_states', 'params'));
    }

    /**
     * ベッド状況確認 検索
     *
     * @param  \App\Http\Requests\Plan\SearchBedStatesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchBedStatesRequest $request): RedirectResponse
    {
        $request->session()->put('plan.bed_states.search', $request->all());
        return redirect()->route('plan.bed_states.index');
    }

    /**
     * ベッド状況確認 登録
     *
     * @param  \App\Http\Requests\Plan\CreateBedStateRequest $request
     * @return void
     */
    public function create(CreateBedStateRequest $request): void
    {
        if ($request->ajax()) {
            $factory_species = $this->factory_service->find($request->factory_code)
                ->factory_species
                ->findByFactorySpeciesCode($request->factory_species_code);

            $this->bed_state_service
                ->createBedState($factory_species, WorkingDate::parse($request->start_of_week)->startOfWeek());

            return;
        }

        abort(403);
    }

    /**
     * ベッド状況確認 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, BedState $bed_state): RedirectResponse
    {
        try {
            $this->bed_state_service->deleteBedState($bed_state);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * ベッド状況確認 各階栽培株数一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @return \Illuminate\View\View
     */
    public function cultivationStates(Request $request, BedState $bed_state): View
    {
        $working_dates = $bed_state->getWorkingDates();
        return view('plan.bed_states.cultivation_states')->with(compact('bed_state', 'working_dates'));
    }

    /**
     * ベッド状況確認 各階栽培株数一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @return \Illuminate\View\View
     */
    public function cultivationStatesSum(Request $request, BedState $bed_state): View
    {
        $working_dates = $bed_state->getWorkingDates();
        return view('plan.bed_states.cultivation_states_sum')->with(compact('bed_state', 'working_dates'));
    }

    /**
     * ベッド状況確認 各階栽培株数 出力
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     */
    public function exportCultivationStates(Request $request, BedState $bed_state)
    {
        $this->bed_state_service->exportCultivationStates($bed_state);
    }

    /**
     * ベッド状況確認 パネル配置図
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function arrangementStates(Request $request, BedState $bed_state, WorkingDate $working_date)
    {
        if (! $bed_state->canReferOnTheDate($working_date)) {
            $working_dates = $bed_state->getWorkingDates();

            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                head($working_dates)->format('Y/m/d'),
                last($working_dates)->format('Y/m/d')
            );

            return redirect()
                ->route('plan.bed_states.arrangement_states.index', [
                    $bed_state->getJoinedPrimaryKeys(),
                    head($working_dates)->format('Y-m-d')
                ])
                ->with(compact('alert'));
        }

        $factory_growing_stages = $bed_state->getFactoryGrowingStagesOnTheDate($working_date);
        $factory_layout = $this->factory_service->getFactroyLayoutWithBedStates(
            $bed_state->factory_species->factory,
            $factory_growing_stages,
            $bed_state->arrangement_states->filterByWorkingDate($working_date)
        );

        return view('plan.bed_states.arrangement_states')->with(compact(
            'bed_state',
            'working_date',
            'factory_growing_stages',
            'factory_layout'
        ));
    }

    /**
     * ベッド状況確認 パネル配置図 出力
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     */
    public function exportArrangementStates(Request $request, BedState $bed_state, WorkingDate $working_date)
    {
        $factory_growing_stages = $bed_state->getFactoryGrowingStagesOnTheDate($working_date);
        $factory_layout = $this->factory_service->getFactroyLayoutWithBedStates(
            $bed_state->factory_species->factory,
            $factory_growing_stages,
            $bed_state->arrangement_states->filterByWorkingDate($working_date)
        );

        return $this->bed_state_service->exportArrangementStates(
            $bed_state,
            $working_date,
            $factory_growing_stages,
            $factory_layout,
            $request->label_of_bed
        );
    }

    /**
     * ベッド状況確認 パネル配置図詳細
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param  int $floor
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function arrangementStatesDetail(
        Request $request,
        BedState $bed_state,
        WorkingDate $working_date,
        int $floor
    ) {
        if (! $bed_state->canReferOnTheDate($working_date)) {
            $working_dates = $bed_state->getWorkingDates();

            $alert = $this->operations['out_of_range'];
            $alert['message'] = sprintf(
                $alert['message'],
                head($working_dates)->format('Y/m/d'),
                last($working_dates)->format('Y/m/d')
            );

            return redirect()
                ->route('plan.bed_states.arrangement_states.detail', [
                    $bed_state->getJoinedPrimaryKeys(),
                    head($working_dates)->format('Y-m-d'),
                    $floor
                ])
                ->with(compact('alert'));
        }

        $factory = $bed_state->factory_species->factory;
        $factory_layout = $this->factory_service->getFactroyLayoutWithDetailBedStates(
            $factory,
            $bed_state->getFactoryGrowingStagesOnTheDate($working_date),
            $bed_state->arrangement_detail_states->filterByWorkingDate($working_date),
            $floor
        );

        return view('plan.bed_states.arrangement_detail_states')->with(compact(
            'bed_state',
            'working_date',
            'factory',
            'factory_layout'
        ));
    }

    /**
     * 栽培パネル配置図詳細 帳票
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Plan\BedState $bed_state
     * @param  \App\ValueObjects\Date\WorkingDate $working_date
     * @param int $floor
     */
    public function exportArrangementStatesDetail(
        Request $request,
        BedState $bed_state,
        WorkingDate $working_date,
        int $floor
    ) {
        $factory_growing_stages = $bed_state->getFactoryGrowingStagesOnTheDate($working_date);
        $factory_layout = $this->factory_service->getFactroyLayoutWithDetailBedStatesToExport(
            $bed_state->factory_species->factory,
            $factory_growing_stages,
            $bed_state->arrangement_detail_states->filterByWorkingDate($working_date),
            $floor
        );

        return $this->bed_state_service->exportArrangementDetailStates(
            $bed_state,
            $working_date,
            $factory_growing_stages,
            $factory_layout
        );
    }
}
