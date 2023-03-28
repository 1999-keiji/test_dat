@extends('layout')

@section('title')
{{ __('view.order.order_forecasts.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.order_forecasts.index') }}</li>
@endsection

@section('content')
@if (session('messages'))
<div class="row">
  <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
    <div class="alert alert-info">
      <ul>
        @foreach (session('messages') as $message)
        <li>{{ $message }}</li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endif

<div class="row">
  <form id="forecast-excel-export-form" class="form-horizontal basic-form export-data-form" action="{{ route('order.order_forecasts.export') }}" method="GET">
    <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-1 col-sm-offset-1">
      <export-forecast-excel-form
        :factories="{{ $factories }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :default-delivery-date="{{ json_encode($delivery_date->startOfWeek()->toDateString()) }}"
        :week-term-of-export-order-forecast="{{ json_encode($delivery_date->getWeekTermOfExportOrderForecast()) }}">
      </export-forecast-excel-form>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-3">
      <button class="btn btn-lg btn-default pull-right export-data" type="button">
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>

@canSave (Auth::user(), route_relatively('order.order_forecasts.index'))
<div class="row">
  <form id="forecast-excel-import-form" class="form-horizontal basic-form import-data-form" action="{{ route('order.order_forecasts.import') }}" method="POST" enctype="multipart/form-data">
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tbody>
          <tr>
            <th>
              {{ __('view.global.file') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <input type="text" id="file_name_view" name="file_name_view" disabled>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-1 col-sm-1 col-xs-1">
      <input id="file_select_hidden" name="import_file" type="file">
      <button id="file_select_btn" class="btn btn-lg btn-default">{{ __('view.global.refer') }}</button>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-3">
      <button class="btn btn-lg btn-default pull-right import-data" type="button">
        <i class="fa fa-upload"></i> {{ __('view.global.excel_upload') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>
@endcanSave
@endsection
