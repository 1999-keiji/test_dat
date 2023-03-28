@extends('layout')

@section('title')
{{ __('view.factory_production_work.work_instruction.index') }}
@endsection

@section('menu-section')
{{ __('view.index.factory-production-work') }}
@endsection

@section('breadcrumbs')
<li>{{ __('view.factory_production_work.work_instruction.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form id="forecast-excel-export-form" class="form-horizontal basic-form export-data-form" action="{{ route('factory_production_work.work_instruction.export') }}" method="GET">
    <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
      <export-work-instruction-excel-form
        :factories="{{ $factories }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
      </export-work-instruction-excel-form>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
      <button class="btn btn-lg btn-default pull-right export-data" type="button">
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>
@endsection
