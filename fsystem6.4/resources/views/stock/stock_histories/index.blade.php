@extends('layout')

@section('title')
{{ __('view.stock.stock_histories.index') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
<li>{{ __('view.stock.stock_histories.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('stock.stock_histories.export') }}" method="GET">
    <search-stock-histories-form
      :factories="{{ $factories }}"
      :input-group-list="{{ json_encode($input_group_list) }}"
      :screens="{{ json_encode($screens) }}"
      :affiliation-list="{{ json_encode($affiliation_list) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
      :errors="{{ $errors }}">
    </search-stock-histories-form>
    <div class="col-md-2 col-sm-2 col-xs-3">
      <button class="btn btn-lg btn-default pull-left" type="submit">
        <i class="fa fa-download"></i>&nbsp;{{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>
@endsection
