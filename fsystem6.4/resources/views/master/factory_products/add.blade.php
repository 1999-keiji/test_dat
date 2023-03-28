@extends('layout')

@section('title')
{{ __('view.master.factory_products.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
<li>
  <a href="{{ route('master.factories.index') }}">{{ __('view.master.factories.index') }}</a>
</li>
<li>
  <a href="{{ route('master.factories.edit', $factory->factory_code) }}">{{ __('view.master.factories.edit') }}</a>
</li>
<li>{{ __('view.master.factory_products.index') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factories.edit', $factory->factory_code) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  @canSave(Auth::user(), route_relatively('master.factories.index'))
  <div class="col-md-5 col-sm-5 col-xs-6">
    <a class="btn btn-default btn-lg pull-right" href="{{ route('master.factory_products.add', $factory->factory_code) }}">
      <i class="fa fa-plus"></i> {{ __('view.global.add') }}
    </a>
  </div>
  @endcanSave
</div>

<div class="row">
  <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered">
      <tbody>
        <tr>
          <th>{{ __('view.master.factories.factory_code') }}</th>
          <td>{{ $factory->factory_code }}</td>
          <th>{{ __('view.master.factories.factory_name') }}</th>
          <td>{{ $factory->factory_abbreviation }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<div class="row factory-products-row">
  <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 fp-list-head">
    <table class="table table-color-bordered table-more-condensed set-width-target">
      <thead>
        <tr>
          <th>{{ __('view.global.select') }}</th>
          <th>@sortablelink('species.species_name', __('view.master.species.species_name'))</th>
          <th>@sortablelink('products.product_name', __('view.master.products.product'))</th>
          <th>{{ __('view.master.factory_products.factory_product_name') }}</th>
        </tr>
      </thead>
    </table>
  </div>
  <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1 fp-list-body">
    <form class="form-horizontal basic-form" action="{{ route('master.factory_products.edit', [$factory->factory_code, $factory->factory_code.'|']) }}" method="GET">
      <table class="table table-color-bordered table-more-condensed get-width-target">
        <tbody>
          @foreach ($factory_products as $fp)
            <tr>
              <td>
                <input type="radio" name="factory_product_sequence_number" value="{{ $fp->sequence_number }}">
              </td>
              <td class="text-left">{{ $fp->species_name }}</td>
              <td class="text-left">{{ $fp->product_code }}:&nbsp;{{ $fp->product_name }}</td>
              <td class="text-left">{{ $fp->factory_product_name }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </form>
  </div>
</div>

<add-factory-product
  action-of-save-factory-product="{{ route('master.factory_products.create', $factory->factory_code) }}"
  :factory="{{ $factory }}"
  :species-list="{{ $species_list }}"
  :currencies="{{ $currencies }}"
  :input-group-list="{{ json_encode($input_group_list) }}"
  :unit-list="{{ json_encode($unit_list) }}"
  :unit-price="{{ $unit_price->toJson() }}"
  :cost="{{ $cost->toJson() }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}">
</add-factory-product>
@endsection
