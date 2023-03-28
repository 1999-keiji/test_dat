<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use InvalidArgumentException;
use PDOException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveFactoryRestRequest;
use App\Models\Master\Factory;
use App\Services\Master\FactoryRestService;
use App\ValueObjects\Date\WorkingDate;

class FactoryRestController extends Controller
{
    /**
     * @var \App\Services\Master\FactoryRestService
     */
    private $factory_rest_service;

    /**
     * @param \App\Services\Master\FactoryRestService $factory_rest_service
     */
    public function __construct(FactoryRestService $factory_rest_service)
    {
        parent::__construct();

        $this->factory_rest_service = $factory_rest_service;
    }

    /**
     * 工場カレンダー
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, Factory $factory)
    {
        $working_date = (new WorkingDate())->firstOfMOnth();
        if ($request->working_date) {
            try {
                $working_date = WorkingDate::parse($request->working_date)->firstOfMonth();
            } catch (InvalidArgumentException $e) {
                return redirect()->route('master.factory_rest.index', $factory->factory_code);
            }
        }

        $working_dates = $this->factory_rest_service->getFactoryRest($factory, $working_date);
        return view('master.factory_rest.index')->with(compact('factory', 'working_date', 'working_dates'));
    }

    /**
     * 工場カレンダー 登録
     *
     * @param  \App\Http\Requests\Master\SaveFactoryRestRequest $request
     * @param  \App\Models\Master\Factory $factory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SaveFactoryRestRequest $request, Factory $factory): RedirectResponse
    {
        try {
            $this->factory_rest_service->saveFactoryRest($factory, $request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->withInput()->with(['alert' => $this->operations['success']]);
    }
}
