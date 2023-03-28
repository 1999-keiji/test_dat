<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateFactoryProductRequest;
use App\Http\Requests\Master\UpdateFactoryProductRequest;
use App\Models\Master\Factory;
use App\Models\Master\FactoryProduct;
use App\Models\Master\FactoryProductPrice;
use App\Services\Master\FactoryService;
use App\Services\Master\FactoryProductService;
use App\Services\Master\SpeciesService;

class FactoryProductsController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryService
     */
    private $factory_service;

    /**
     * @var \App\Services\Master\SpeciesService
     */
    private $species_service;

    /**
     * @var \App\Services\Master\FactoryProductService
     */
    private $factory_product_service;

    /**
     * @param  \App\Services\Master\FactoryProductService
     * @return void
     */
    public function __construct(
        FactoryService $factory_service,
        SpeciesService $species_service,
        FactoryProductService $factory_product_service
    ) {
        parent::__construct();

        $this->factory_service = $factory_service;
        $this->species_service = $species_service;
        $this->factory_product_service = $factory_product_service;
    }

    /**
     * 工場取扱商品 一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Factory $factory): View
    {
        $factory_products = $this->factory_product_service->getFactoryProductsByFactoryCode(
            $factory->factory_code,
            $request->only(['order', 'sort'])
        );

        return view('master.factory_products.index')->with(compact('factory', 'factory_products'));
    }

    /**
     * 工場取扱商品マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\View\View
     */
    public function add(Request $request, Factory $factory): View
    {
        $factory_products = $this->factory_product_service->getFactoryProductsByFactoryCode(
            $factory->factory_code,
            $request->only(['order', 'sort'])
        );

        return view('master.factory_products.add')->with(compact('factory', 'factory_products'));
    }

    /**
     * 工場取扱商品マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateFactoryProductRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateFactoryProductRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $factory_product = $this->factory_product_service->createFactoryProduct($factory, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.factory_products.edit', [
                $factory->factory_code,
                $factory_product->getJoinedPrimaryKeys()
            ])
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場取扱商品マスタ 修正
     *
     * @param  \App\Http\Requests\Master\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Factory $factory, FactoryProduct $factory_product): View
    {
        $factory_products = $this->factory_product_service->getFactoryProductsByFactoryCode(
            $factory->factory_code,
            $request->only(['order', 'sort'])
        );

        return view('master.factory_products.edit')->with(compact('factory', 'factory_products', 'factory_product'));
    }

    /**
     * 工場取扱商品マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateFactoryProductRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateFactoryProductRequest $request,
        Factory $factory,
        FactoryProduct $factory_product
    ): RedirectResponse {
        $params = $request->all();
        if (! $this->factory_product_service->isUpdatableFactoryProduct($factory_product, $params)) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $factory_product = $this->factory_product_service->updateFactoryProduct($factory_product, $params);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()
            ->route('master.factory_products.edit', [
                $factory->factory_code,
                $factory_product->getJoinedPrimaryKeys()
            ])
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * 工場取扱商品マスタ 削除
     *
     * @param  \App\Http\Requests\Master\Request $request
     * @param  \App\Models\Master\Factory $factory
     * @param  \App\Models\Master\FactoryProduct $factory_product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Factory $factory, FactoryProduct $factory_product): RedirectResponse
    {
        try {
            if (! $factory_product->isDeletable()) {
                return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
            }

            $this->factory_product_service->deleteFactoryProduct($factory_product);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.factory_products.index', $factory->factory_code)
            ->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 工場取扱商品検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getFactoryProducts(Request $request): array
    {
        if ($request->ajax()) {
            return $this->factory_product_service->getFactoryProductsForSearchingApi($request->all());
        }

        abort(404);
    }

    /**
     * API用 工場商品価格検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\Master\FactoryProductPrice
     */
    public function getAppliedFactoryProductPrice(Request $request): ?FactoryProductPrice
    {
        if ($request->ajax()) {
            return $this->factory_product_service->getAppliedFactoryProductPrice($request->all());
        }

        abort(404);
    }

    /**
     * API用 商品規格検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getPackagingStylesWithFactoryCodeAndSpeciesCode(Request $request): array
    {
        if ($request->ajax()) {
            if (! $request->factory_code || ! $request->species_code) {
                return [];
            }

            $factory = $this->factory_service->find($request->factory_code);
            $species = $this->species_service->find($request->species_code);

            return $factory->factory_products->getPackagingStylesBySpecies($species);
        }

        abort(404);
    }
}
