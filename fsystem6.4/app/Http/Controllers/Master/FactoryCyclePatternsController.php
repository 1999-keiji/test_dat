<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\UpdateFactoryCyclePatternRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactoryCyclePattern;
use App\Services\Master\FactoryCyclePatternService;

class FactoryCyclePatternsController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryCyclePatternService
     */
    private $factory_cycle_pattern_service;

    /**
     * @param  \App\Services\Master\FactoryCyclePatternService $factory_cycle_pattern_service
     * @return void
     */
    public function __construct(FactoryCyclePatternService $factory_cycle_pattern_service)
    {
        parent::__construct();

        $this->factory_cycle_pattern_service = $factory_cycle_pattern_service;
    }

    /**
     * 工場マスタ サイクルパターンタブ 表示
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        return view('master.factories.cycle_patterns')->with(compact('factory'));
    }

    /**
     * 工場サイクルパターン更新
     *
     * @param  \App\Http\Requests\Master\UpdateFactoryCyclePatternRequest
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateFactoryCyclePatternRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_cycle_pattern_service->saveCyclePattern($factory, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場サイクルパターン削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryCyclePattern $factory_cycle_pattern
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(
        Request $request,
        Factory $factory,
        FactoryCyclePattern $factory_cycle_pattern
    ): RedirectResponse {
        try {
            if (! $factory_cycle_pattern->isDeletable()) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['forbidden']]);
            }

            $this->factory_cycle_pattern_service->deleteCyclePattern($factory_cycle_pattern);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場サイクルパターン詳細検索API
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getFactoryCyclePatternItems(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_cycle_pattern_service
                ->getFactoryCyclePatternItemsForApi(
                    $request->factory_code,
                    (int)$request->cycle_pattern_sequence_number
                );
        }

        abort(404);
    }
}
