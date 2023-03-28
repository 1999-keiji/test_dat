@extends('layout')

@section('title')
{{ __('view.master.factory_rest.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
<li><a href="{{ route('master.factories.index') }}">{{ __('view.master.factories.index') }}</a></li>
<li><a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.factories.edit') }}</a></li>
<li>{{ __('view.master.factory_rest.index') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factories.edit', $factory->factory_code) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered">
      <tr>
        <th>{{ __('view.master.factories.factory_code') }}</th>
        <td><label>{{ $factory->factory_code }}</label></td>
        <th>{{ __('view.master.factories.factory_name') }}</th>
        <td><label>{{ $factory->factory_abbreviation }}</label></td>
      </tr>
    </table>
  </div>
</div>

<div class="row">
  <div class="text-center">
    <form action="{{ route('master.factory_rest.index', $factory->factory_code) }}" method="GET">
      <button class="btn btn-default btn-lg move-btn" name="working_date" type="submit" value="{{ $working_date->subYear()->format('Y-m-d') }}">
        <i class="fa fa-angle-double-left"></i> {{ __('view.master.global.prev_year') }}
      </button>
      <button class="btn btn-default btn-lg move-btn right-margined-btn" name="working_date" type="submit" value="{{ $working_date->subMonth()->format('Y-m-d') }}">
        <i class="fa fa-angle-left"></i> {{ __('view.master.global.prev_month') }}
      </button>
      <label class="h2">{{ $working_date->format('Y/n') }}</label>
      <button class="btn btn-default btn-lg move-btn" name="working_date" type="submit" value="{{ $working_date->addMonth()->format('Y-m-d') }}">
        {{ __('view.master.global.next_month') }} <i class="fa fa-angle-right"></i>
      </button>
      <button class="btn btn-default btn-lg move-btn" name="working_date" type="submit" value="{{ $working_date->addYear()->format('Y-m-d') }}">
        {{ __('view.master.global.next_year') }} <i class="fa fa-angle-double-right"></i>
      </button>
    </form>
  </div>
</div>

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
              <save-factory-rest-form
                :date="{{ json_encode($wd['working_date']) }}"
                :factory-rest="{{ $wd['factory_rest'] }}"
                :can-save-factory="{{ json_encode(Auth::user()->canSave(route_relatively('master.factories.index'))) }}">
              </save-factory-rest-form>
              <br>
              <label>{{ implode(',', $wd['factory_rest']->getRestList()) }}</label>
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
