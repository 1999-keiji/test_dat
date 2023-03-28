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
use App\Http\Requests\Master\CreateProductRequest;
use App\Http\Requests\Master\SearchProductsRequest;
use App\Http\Requests\Master\UpdateProductRequest;
use App\Models\Master\Product;
use App\Services\Master\ProductService;

class ProductsController extends Controller
{
    /**
     * @var \App\Services\Master\ProductService
     */
    private $product_service;

    /**
     * @param  \App\Services\Master\ProductService $product_service
     * @return void
     */
    public function __construct(ProductService $product_service)
    {
        parent::__construct();

        $this->product_service = $product_service;
    }

    /**
     * 商品マスタ 一覧
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $products = [];

        $params = $request->session()->get('master.products.search', []);
        if (count($params) !== 0) {
            $page = $request->page ?: 1;

            try {
                $products = $this->product_service->searchProducts($params, (int)$page);
            } catch (PageOverException $e) {
                $request->session()->reflash();
                return redirect()->route('master.products.index');
            }
        }

        return view('master.products.index')->with(compact('products', 'params'));
    }

    /**
     * 商品マスタ 検索
     *
     * @param  \App\Http\Requests\Master\SearchProductsRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchProductsRequest $request): RedirectResponse
    {
        $request->session()->put('master.products.search', $request->all());
        return redirect()->route('master.products.index');
    }

    /**
     * 商品マスタ 追加
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function add(Request $request): View
    {
        return view('master.products.add');
    }

    /**
     * 商品マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateProductRequest $request): RedirectResponse
    {
        try {
            $product = $this->product_service->createProduct($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.products.edit', $product->product_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 商品マスタ 修正
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Product $product): View
    {
        return view('master.products.edit')->with(compact('product'));
    }

    /**
     * 商品マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateProductRequest $request
     * @param  \App\Models\Master\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->product_service->updateProduct($product, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->route('master.products.edit', $product->product_code)->with([
            'alert' => $this->operations['success']
        ]);
    }

    /**
     * 商品マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Product $product
     * @return Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Product $product): RedirectResponse
    {
        if (! $product->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->product_service->deleteProduct($product);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 商品検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getProducts(Request $request): array
    {
        if ($request->ajax()) {
            return $this->product_service->getProductsForSearchingApi($request->all());
        }

        abort(404);
    }

    /**
     * API用 商品価格検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getProductPrices(Request $request): array
    {
        if ($request->ajax()) {
            return $this->product_service->getProductPricesForSearchingApi($request->all());
        }

        abort(404);
    }

    /**
     * API用 商品特価検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getProductSpecialPrices(Request $request): array
    {
        if ($request->ajax()) {
            return $this->product_service->getProductSpecialPricesForSearchingApi($request->all());
        }

        abort(404);
    }
}
