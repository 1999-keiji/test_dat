@extends('layout')

@section('title')
{{ __('view.plan.growth_sale_management.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_sale_management.index') }}</li>
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
  <form id="forecast-excel-export-form" class="form-horizontal basic-form export-data-form" action="{{ route('plan.growth_sale_management.export') }}" method="GET">
    <div class="col-md-8 col-sm-8 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <management-factory-species
        :factories="{{ $factories }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        default-harvesting-date="{{ $default_harvesting_date }}">
      </management-factory-species>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-1 col-sm-offset-1">
      <button class="btn btn-lg btn-default pull-right export-data" type="button">
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>

@canSave (Auth::user(), route_relatively('plan.growth_sale_management.index'))
<div class="row">
  <form id="forecast-excel-import-form" class="form-horizontal basic-form import-data-form" action="{{ route('plan.growth_sale_management.import') }}" method="POST" enctype="multipart/form-data">
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tbody>
          <tr>
            <th>ファイル</th>
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
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-2 col-sm-offset-2">
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
