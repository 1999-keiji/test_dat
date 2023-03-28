<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateDeliveryFactoryProductRequest;
use App\Http\Requests\Master\UpdateDeliveryFactoryProductRequest;
use App\Models\Master\DeliveryDestination;
use App\Models\Master\DeliveryFactoryProduct;
use App\Models\Master\FactoryProductSpecialPrice;
use App\Services\Master\DeliveryFactoryProductService;
use App\Services\Master\FactoryProductService;

class DeliveryFactoryProductsController extends Controller
{
    /**
     * @var \App\Services\Master\DeliveryFactoryProductService
     */
    private $delivery_factory_product_service;

    /**
     * @var \App\Services\Master\FactoryProductService
     */
    private $factory_product_service;

    /**
     * @param  \App\Services\Master\FactoryProductSpecialPriceService
     * @param  \App\Services\Master\FactoryProductService $factory_product_service
     * @return void
     */
    public function __construct(
        DeliveryFactoryProductService $delivery_factory_product_service,
        FactoryProductService $factory_product_service
    ) {
        parent::__construct();

        $this->delivery_factory_product_service = $delivery_factory_product_service;
        $this->factory_product_service = $factory_product_service;
    }

    /**
     * 納入工場商品マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateDeliveryFactoryProductRequest $request
     * @param  \App\Models\Master\DeliveryDestination $delivery_destination
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(
        CreateDeliveryFactoryProductRequest $request,
        DeliveryDestination $delivery_destination
    ): RedirectResponse {
        try {
            $params = $request->all();
            if (! $this->delivery_factory_product_service->isNotOverlappedApplicationTerm($params)) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['overlapped']]);
            }

            $factory_product = $this->factory_product_service->findFactoryProduct($params);
            if (! $this->delivery_factory_product_service->canLinkFactoryProduct(
                $delivery_destination,
                $factory_product
            )) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['forbidden']]);
            }

            $this->delivery_factory_product_service->createDeliveryFactoryProduct($params);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入工場商品マスタ 修正
     *
     * @param  \App\Http\Requests\Master\UpdateDeliveryFactoryProductRequest $request
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateDeliveryFactoryProductRequest $request,
        DeliveryFactoryProduct $delivery_factory_product
    ): RedirectResponse {
        try {
            $params = $request->all();
            if (! $this->delivery_factory_product_service->isNotOverlappedApplicationTerm($params)) {
                return redirect()->back()->withInput()->with(['alert' => $this->operations['overlapped']]);
            }

            $this->delivery_factory_product_service->updateDeliveryFactoryProduct($delivery_factory_product, $params);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入工場商品マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\DeliveryFactoryProduct $delivery_factory_product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, DeliveryFactoryProduct $delivery_factory_product): RedirectResponse
    {
        try {
            $this->delivery_factory_product_service->deleteDeliveryFactoryProduct($delivery_factory_product);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 納入工場商品検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getDeliveryFactoryProducts(Request $request): array
    {
        if ($request->ajax()) {
            return $this->delivery_factory_product_service
                ->getDeliveryFactoryProductsByDeliveryDestinationAndFactory($request->all());
        }

        abort(404);
    }

    /**
     * API用 工場商品特価検索
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Models\Master\FactoryProductSpecialPrice
     */
    public function getAppliedFactoryProductSpecialPrice(Request $request): ?FactoryProductSpecialPrice
    {
        if ($request->ajax()) {
            return $this->delivery_factory_product_service->getAppliedFactoryProductSpecialPrice($request->all());
        }

        abort(404);
    }
}
