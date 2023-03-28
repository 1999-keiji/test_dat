@extends('layout')

@section('title')
{{ __('view.stock.global.stock') }}{{ __('view.stock.stocks.adjust') }}
@endsection

@section('menu-section')
{{ __('view.index.stock') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('stock.stocks.index') }}">
      {{ __('view.stock.stocks.index') }}
    </a>
  </li>
  <li>{{ __('view.stock.global.stock') }}{{ __('view.stock.stocks.adjust') }}</li>
@endsection

@section('content')
<form class="form-horizontal basic-form save-data-form" action="{{ route('stock.stocks.adjust', $stock->stock_id) }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('stock.stocks.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3 col-sm-4 col-xs-6 col-md-offset-2 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factories.factory') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $stock->factory->factory_abbreviation }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.warehouses.warehouse_storage') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $stock->warehouse->warehouse_abbreviation }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3 col-sm-4 col-xs-6 col-md-offset-2 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.species.species') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $stock->species->species_name }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-6">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.shipment.global.harvesting_date') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $stock->getHarvestingDate() }}</span>
        </div>
      </div>
    </div>
  </div>

  <adjust-stock-form
    :stock="{{ $stock }}"
    :input-group-list="{{ json_encode($input_group_list) }}"
    :stock-status-list="{{ json_encode(array_flip($stock_status_list)) }}"
    :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
    :errors="{{ $errors }}">
  </adjust-stock-form>
  <input name="updated_at" type="hidden" value="{{ $stock->updated_at }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
</form>
@endsection
