<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use InvalidArgumentException;
use PDOException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\SaveCalendarRequest;
use App\Models\Master\Calendar;
use App\Services\Master\CalendarService;
use App\ValueObjects\Date\WorkingDate;

class CalendarsController extends Controller
{
    /**
     * @var \App\Services\Master\CalendarService
     */
    private $calendar_service;

    /**
     * @param  \App\Services\Master\CalendarService $calendar_service
     * @return void
     */
    public function __construct(CalendarService $calendar_service)
    {
        parent::__construct();

        $this->calendar_service = $calendar_service;
    }

    /**
     * カレンダ一覧
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $working_date = (new WorkingDate())->firstOfMOnth();

        if ($request->working_date) {
            try {
                $working_date = WorkingDate::parse($request->working_date)->firstOfMonth();
            } catch (InvalidArgumentException $e) {
                return redirect()->route('master.calndars.index', $factory->factory_code);
            }
        }

        $working_dates = [];
        if ($request->event_class) {
            $working_dates = $this->calendar_service->getCalendarEvents($working_date, (int)$request->event_class);
        }

        return view('master.calendars.index')->with(compact('factory', 'working_date', 'working_dates'));
    }

    /**
     * カレンダー 登録
     *
     * @param  \App\Http\Requests\Master\SaveCalendarRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(SaveCalendarRequest $request): RedirectResponse
    {
        try {
            $this->calendar_service->saveCalendarEvent($request->all());
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->withInput()->with(['alert' => $this->operations['success']]);
    }

    /**
     * カレンダー 削除
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Master\Calendar $calendar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Calendar $calendar)
    {
        try {
            $this->calendar_service->deleteCalendarEvent($calendar);
        } catch (PDOException $e) {
            report($e);
            return redirect()->back()->withInput()->with(['alert' => $this->operations['fail']]);
        }

        return redirect()->back()->withInput()->with(['alert' => $this->operations['success']]);
    }
}
