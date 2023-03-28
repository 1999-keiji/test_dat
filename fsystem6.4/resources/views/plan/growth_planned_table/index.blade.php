@extends('layout')

@section('title')
{{ __('view.plan.growth_planned_table.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_planned_table.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('plan.growth_planned_table.export') }}" method="GET">
    <div class="col-md-7 col-sm-7 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <export-growth-planned-table
        :factories="{{ $factories }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}">
      </export-growth-planned-table>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <button class="btn btn-lg btn-default pull-right remove-alert" type="submit">
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>
@endsection
