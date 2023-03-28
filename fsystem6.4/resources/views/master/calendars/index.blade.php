@extends('layout')

@section('title')
{{ __('view.master.calendars.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.calendars.index') }}</li>
@endsection

@section('content')
<form action="{{ route('master.calendars.index') }}" action="GET">
  <div class="row">
    <div class="col-md-3 col-sm-5 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">
            {{ __('view.master.calendars.event_class') }}
            <span class="required-mark">*</span>
          </th>
          <td class="col-md-3 col-sm-3 col-xs-3">
            <select id="event_class" class="form-control {{ has_error('event_class') }}" name="event_class">
              <option value=""></option>
              @foreach ($event_class_list as $label =>  $value)
              <option value="{{ $value }}"  {{ is_selected($value, old('event_class', request('event_class'))) }}>{{ $label }}</option>
              @endforeach
            </select>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="text-center">
      <button class="btn btn-default btn-lg move-btn" type="submit" name="working_date" value="{{ $working_date->subYear()->format('Y-m-d') }}">
        <i class="fa fa-angle-double-left"></i> {{ __('view.master.global.prev_year') }}
      </button>
      <button class="btn btn-default btn-lg move-btn right-margined-btn" type="submit" name="working_date" value="{{ $working_date->subMonth()->format('Y-m-d') }}">
        <i class="fa fa-angle-left"></i> {{ __('view.master.global.prev_month') }}
      </button>
      <label class="h2">{{ $working_date->format('Y/n') }}</label>
      <button class="btn btn-default btn-lg move-btn" type="submit" name="working_date" value="{{ $working_date->addMonth()->format('Y-m-d') }}">
        {{ __('view.master.global.next_month') }} <i class="fa fa-angle-right"></i>
      </button>
      <button class="btn btn-default btn-lg move-btn" type="submit" name="working_date" value="{{ $working_date->addYear()->format('Y-m-d') }}">
        {{ __('view.master.global.next_year') }} <i class="fa fa-angle-double-right"></i>
      </button>
    </div>
  </div>
</form>

@if (count($working_dates) !== 0)
<div class="row">
  <div class="col-sm-10 col-sm-offset-1">
    <table class="table table-color-bordered table-layout-fixed">
      <tr>
        @foreach (head($working_dates) as $date)
          <th class="
            @if ($date['working_date']->isSaturday())
            text-info
            @endif
            @if ($date['working_date']->isSunday())
            text-danger
            @endif">
            {{ $date['working_date']->dayOfWeekJa() }}
          </th>
          @endforeach
      </tr>
      @foreach ($working_dates as $working_dates_per_week)
      <tr>
        @foreach ($working_dates_per_week as $wd)
          @if ($wd['working_date']->format('m') === $working_date->format('m'))
            <td>
              <save-event-form
                :date="{{ json_encode($wd['working_date']) }}"
                :calendar="{{ $wd['calendar'] }}"
                :event-class="{{ json_encode((int)request('event_class')) }}"
                :event-class-list="{{ json_encode(array_flip($event_class_list)) }}"
                action-of-delete-event="{{ route('master.calendars.delete', $wd['calendar']->date) }}"
                :can-save-event="{{ json_encode(Auth::user()->canSave(route_relatively('master.calendars.index'))) }}">
              </save-event-form>
              <br>
              <label>{{ $wd['calendar']->event }}</label>
            </td>
          @else
            <td class="active">
              {{ $wd['working_date']->format('n/j') }}<br><br><br>
            </td>
          @endif
        @endforeach
      <tr>
      @endforeach
    </table>
  </div>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $(function () {
      $('#event_class').change(function () {
        $(this).parents('form').submit()
      })
    })
  </script>
@endsection
