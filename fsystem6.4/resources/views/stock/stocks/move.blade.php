@extends('layout')

@section('title')
{{ __('view.stock.global.stock') }}{{ __('view.stock.stocks.move') }}
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
  <li>{{ __('view.stock.global.stock') }}{{ __('view.stock.stocks.move') }}</li>
@endsection

@section('content')
<form class="form-horizontal basic-form save-data-form" action="{{ route('stock.stocks.move', $stock->stock_id) }}" method="POST">
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
    <div class="col-md-4 col-sm-4 col-xs-6 col-md-offset-2 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factories.factory') }}
        </label>
        <div class="col-md-8 col-sm-8">
          <span class="shown_label">{{ $stock->factory->factory_abbreviation }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
      <div class="row form-group">
        <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.species.species') }}
        </label>
        <div class="col-md-8 col-sm-8">
          <span class="shown_label">{{ $stock->species->species_name }}</span>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-4 col-xs-6 col-md-offset-2 col-sm-offset-1">
      <div class="row form-group">
        <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.factory_products.packaging_style') }}
        </label>
        <div class="col-md-8 col-sm-8">
          <span class="shown_label">
            {{ $stock->number_of_heads }}{{ __('view.global.stock') }}
              {{ $stock->weight_per_number_of_heads }}g
              {{ $input_group_list[$stock->input_group] }}
            </span>
          </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-6">
      <div class="row form-group">
        <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.shipment.global.harvesting_date') }}
        </label>
        <div class="col-md-8 col-sm-8">
          <span class="shown_label">{{ $stock->getHarvestingDate() }}</span>
        </div>
      </div>
    </div>
  </div>

  <move-stock-form
    :stock="{{ $stock }}"
    :warehouse="{{ $stock->warehouse }}"
    :factory-warehouses="{{ $stock->factory->getFactoryWarehouses() }}"
    :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
    :errors="{{ $errors }}"
    :will-export-moving-stock-file="{{ json_encode(session()->has('stock.stocks.move.export_file')) }}"
    export-moving-stock-action="{{ route('stock.stocks.move.export', $stock->stock_id) }}">
  </move-stock-form>
  <input name="updated_at" type="hidden" value="{{ $stock->updated_at }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
</form>
@endsection
