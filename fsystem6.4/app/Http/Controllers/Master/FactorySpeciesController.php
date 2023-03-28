<?php

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Master\Factory;
use App\Models\Master\FactorySpecies;
use App\Services\Master\FactorySpeciesService;
use App\Http\Requests\Master\CreateFactorySpeciesRequest;
use App\Http\Requests\Master\UpdateFactorySpeciesRequest;

class FactorySpeciesController extends Controller
{
    /**
     * @var \App\Services\Master\FactorySpeciesService
     */
    private $factory_species_service;

    /**
     * @param  \App\Services\Master\FactorySpeciesService
     * @return void
     */
    public function __construct(FactorySpeciesService $factory_species_service)
    {
        parent::__construct();

        $this->factory_species_service = $factory_species_service;
    }

    /**
     * 工場取扱品種 一覧
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        $factory_species_list = $this->factory_species_service->getFactorySpecies([
            'factory_code' => $factory->factory_code
        ]);

        return view('master.factory_species.index')->with(compact(
            'factory',
            'factory_species_list'
        ));
    }

    /**
     * 工場取扱品種 追加
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function add(Request $request, Factory $factory): View
    {
        $factory_species_list = $this->factory_species_service->getFactorySpecies([
            'factory_code' => $factory->factory_code
        ]);

        return view('master.factory_species.add')->with(compact(
            'factory',
            'factory_species_list'
        ));
    }

    /**
     * 工場取扱品種 登録
     *
     * @param  \App\Http\Requests\Master\CreateFactorySpeciesRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateFactorySpeciesRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $factory_species = $this->factory_species_service->createFactorySpecies($factory, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.factory_species.edit', [
                $factory->factory_code,
                $factory_species->getJoinedPrimaryKeys()
            ])
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場取扱品種 編集
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Factory $factory, FactorySpecies $factory_species): View
    {
        $factory_species_list = $this->factory_species_service->getFactorySpecies([
            'factory_code' => $factory->factory_code
        ]);

        return view('master.factory_species.edit')->with(compact(
            'factory',
            'factory_species',
            'factory_species_list'
        ));
    }

    /**
     * 工場取扱品種 更新
     *
     * @param  \App\Http\Requests\Master\UpdateFactorySpeciesRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateFactorySpeciesRequest $request,
        Factory $factory,
        FactorySpecies $factory_species
    ): RedirectResponse {
        try {
            $factory_species = $this->factory_species_service
                ->updateFactorySpecies($factory, $factory_species, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場取扱品種 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactorySpecies $factory_species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Factory $factory, FactorySpecies $factory_species): RedirectResponse
    {
        try {
            if (! $factory_species->isDeletable()) {
                return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
            }

            $this->factory_species_service->deleteFactorySpecies($factory_species);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.factory_species.index', $factory->factory_code)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 工場取扱品種検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getFactorySpecies(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_species_service->getFactorySpeciesForSearchingApi($request->all());
        }

        abort(404);
    }

    /**
     * API用 工場コードによる品種検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getSpeciesWithFactoryCode(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_species_service->getSpeciesWithFactoryCodeForSearchingApi($request->all());
        }

        abort(404);
    }

    /**
     * API用 日付と株数を基に生育用の数値をシミュレーション
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function simulateGrowing(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_species_service->simulateGrowing($request->all());
        }

        abort(404);
    }
}
