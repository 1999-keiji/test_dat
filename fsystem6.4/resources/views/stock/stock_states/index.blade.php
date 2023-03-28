@extends('layout')

@section('title')
{{ __('view.stock.stock_states.index') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
<li>{{ __('view.stock.stock_states.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form export-data-form" action="{{ route('stock.stock_states.export') }}" method="GET">
    <search-stock-states-form
      :factories="{{ $factories }}"
      :input-group-list="{{ json_encode($input_group_list) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
      :errors="{{ $errors }}">
    </search-stock-states-form>
    <div class="col-md-2 col-sm-2 col-xs-3">
      <button class="btn btn-lg btn-default pull-left export-data" type="button">
        <i class="fa fa-download"></i>&nbsp;{{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>
@endsection
