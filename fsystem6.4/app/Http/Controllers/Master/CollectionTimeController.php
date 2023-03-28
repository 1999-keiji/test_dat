<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exceptions\OptimisticLockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CreateCollectionTimeRequest;
use App\Http\Requests\Master\UpdateCollectionTimeRequest;
use App\Models\Master\CollectionTime;
use App\Models\Master\TransportCompany;
use App\Services\Master\CollectionTimeService;

class CollectionTimeController extends Controller
{
    /**
     * @var \App\Services\Master\CollectionTimeService
     */
    private $collection_time_service;

    /**
     * @param  \App\Services\Master\CollectionTimeService
     * @return void
     */
    public function __construct(CollectionTimeService $collection_time_service)
    {
        parent::__construct();

        $this->collection_time_service = $collection_time_service;
    }

    /**
     * 運送会社マスタ 集荷時間一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @return \Illuminate\View\View
     */
    public function index(Request $request, TransportCompany $transport_company): View
    {
        return view('master.transport_companies.collection_times')->with(compact('transport_company'));
    }

    /**
     * 集荷時間マスタ 登録
     *
     * @param  \App\Http\Requests\Master\CreateCollectionTimeRequest $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(CreateCollectionTimeRequest $request, TransportCompany $transport_company): RedirectResponse
    {
        try {
            $this->collection_time_service->createCollectionTime($transport_company, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 納入倉庫マスタ 更新
     *
     * @param  \App\Http\Requests\Master\UpdateCollectionTimeRequest $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(
        UpdateCollectionTimeRequest $request,
        TransportCompany $transport_company,
        CollectionTime $collection_time
    ): RedirectResponse {
        try {
            $this->collection_time_service->updateCollectionTime($collection_time, $request->all());
        } catch (OptimisticLockException $e) {
            return redirect()->back()->withInput()->with(['alert' => $this->operations['interuptted']]);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * 集荷時間マスタ 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\TransportCompany $transport_company
     * @param  \App\Models\Master\CollectionTime $collection_time
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(
        Request $request,
        TransportCompany $transport_company,
        CollectionTime $collection_time
    ): RedirectResponse {
        if (! $collection_time->isDeletable()) {
            return redirect()->back()->with(['alert' => $this->operations['forbidden']]);
        }

        try {
            $this->collection_time_service->deleteCollectionTime($collection_time);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->with(['alert' => $this->operations['success']]);
    }

    /**
     * API用 運送会社コードによる集荷時間の取得
     *
     * @param \Illuminate\Http\Request $request
     */
    public function getCollectionTimesByTransportCompany(Request $request)
    {
        if ($request->ajax()) {
            return $this->collection_time_service->getCollectionTimesByTransportCompanyForApi($request->all());
        }

        abort(404);
    }
}
