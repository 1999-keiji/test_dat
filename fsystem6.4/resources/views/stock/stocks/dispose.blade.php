@extends('layout')

@section('title')
{{ __('view.stock.stocks.dispose') }}{{ __('view.global.register') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.stock.stocks.dispose') }}{{ __('view.global.register') }}</li>
@endsection

@section('content')
@if ($params['refered_from_index'] ?? false)
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('stock.stocks.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>
@endif

<search-disposed-stocks-form
  search-disposed-stocks-action="{{ route('stock.stocks.dispose.search') }}"
  export-disposed-stocks-action="{{ route('stock.stocks.dispose.export') }}"
  :factories="{{ $factories }}"
  :stock-status-list="{{ json_encode($stock_status_list) }}"
  :input-group-list="{{ json_encode($input_group_list) }}"
  :disposal-status-list="{{ json_encode($disposal_status_list_except_part_disposal) }}"
  :search-params="{{ json_encode($params ?: new \stdClass()) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}">
</search-disposed-stocks-form>

@if (count($params) !== 0)
<form class="save-data-form" action="{{ route('stock.stocks.dispose') }}" method="POST">
  @canSave (Auth::user(), route_relatively('stock.stocks.dispose.index'))
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <button class="btn btn-default btn-lg save-data pull-right" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
    </div>
  </div>
  @endcanSave
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <dispose-stocks-form
        :species-list="{{ json_encode($species_list) }}"
        :input-group-list="{{ json_encode($input_group_list) }}"
        :stock-status-list="{{ json_encode(array_flip($stock_status_list)) }}"
        :old-params="{{ json_encode(old('stocks') ?: new \stdClass()) }}">
      </dispose-stocks-form>
    </div>
  </div>
  <input name="factory_code" type="hidden" value="{{ $params['factory_code'] }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
</form>
@endif
@endsection
