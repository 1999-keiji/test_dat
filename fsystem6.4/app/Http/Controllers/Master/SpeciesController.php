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
use App\Http\Requests\Master\SearchSpeciesRequest;
use App\Http\Requests\Master\CreateSpeciesRequest;
use App\Http\Requests\Master\UpdateSpeciesRequest;
use App\Models\Master\Species;
use App\Services\Master\SpeciesService;

class SpeciesController extends Controller
{
    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @param  \App\Services\Master\CorporationService $lead_time_service
     * @return void
     */
    public function __construct(SpeciesService $species_service)
    {
        parent::__construct();

        $this->species_service = $species_service;
    }

    /**
     * 品種マスタ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $species_list = [];

        $params = $request->session()->get('master.species.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;
            try {
                $species_list = $this->species_service->searchSpecies($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.species.index');
            }
        }

        return view('master.species.index')->with(compact('species_list', 'params'));
    }

    /**
     * 品種マスタ検索
     *
     * @param \App\Http\Requests\Master\SearchSpeciesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchSpeciesRequest $request): RedirectResponse
    {
        $request->session()->put('master.species.search', $request->all());
        return redirect()->route('master.species.index');
    }

    /**
     * 品種マスタ追加
     *
     * @param \App\Http\Requests\Master\CreateSpeciesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateSpeciesRequest $request): RedirectResponse
    {
        if (!empty($request['product_large_category'])) {
            $species_converter_count = count($request['product_large_category']);
            $category_list =[];
            for ($i = 0; $i < $species_converter_count; $i++) {
                array_push(
                    $category_list,
                    $request['product_large_category'][$i] . ',' . $request['product_middle_category'][$i]
                );
            }
            if (array_unique($category_list) !== $category_list) {
                return redirect()->back()->with(['alert' => $this->operations['distinct']]);
            }
        }

        try {
            $species = $this->species_service->createSpecies($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     *品種マスタ更新
     *
     * @param  \App\Http\Requests\Master\UpdateSpeciesRequest $request
     * @param  \App\Models\Master\Species $species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSpeciesRequest $request, Species $species): RedirectResponse
    {
        if (!empty($request['product_large_category'])) {
            $species_converter_count = count($request['product_large_category']);
            $category_list =[];
            for ($i = 0; $i < $species_converter_count; $i++) {
                array_push(
                    $category_list,
                    $request['product_large_category'][$i] . ',' . $request['product_middle_category'][$i]
                );
            }
            if (array_unique($category_list) !== $category_list) {
                return redirect()->back()->with(['alert' => $this->operations['distinct']]);
            }
        }

        try {
            $species = $this->species_service->updateSpecies($species, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 品種マスタ削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Species $species
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Species $species): RedirectResponse
    {
        if (! $species->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->species_service->deleteSpecies($species);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }
}
