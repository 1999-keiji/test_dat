@extends('layout')

@section('title')
{{ __('view.shipment.productized_results.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.shipment.productized_results.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('shipment.productized_results.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <search-productized-results
        :factories="{{ $factories }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :errors="{{ $errors }}">
      </search-productized-results>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p><span class="search-result-count">
      {{ $productized_results->count() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
</div>
<div class="row">
  <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.input') }}</th>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.shipment.product_allocations.index') }}</th>
          <th class="col-md-3 col-sm-5 col-xs-5">{{ __('view.master.species.species') }}</th>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.shipment.global.harvesting_date') }}</th>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.shipment.productized_results.plans_harvest_stock') }}</th>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.shipment.productized_results.product_harvest_stock') }}</th>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.plan.growth_sale_management.product_rate') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($productized_results as $pr)
        <tr>
          <td class="text-center">
            <a class="btn btn-sm btn-info" href="{{ route('shipment.productized_results.input', [$pr->factory_code, $pr->species_code, $pr->harvesting_date->format('Y-m-d')]) }}">
              {{ __('view.shipment.productized_results.record') }}
            </a>
          </td>
          <td class="text-center">
            @if ($pr->producted_quantity)
              <a class="btn btn-sm btn-warning" href="{{ route('shipment.product_allocations.index', [$pr->factory_code, $pr->species_code, $pr->harvesting_date->format('Y-m-d')]) }}">
                {{ __('view.shipment.product_allocations.allocate') }}
              </a>
            @endif
          </td>
          <td class="text-left">{{ $pr->species_name }}</td>
          <td class="text-center">{{ $pr->harvesting_date->format('Y/m/d') }}</td>
          <td class="text-right">{{ number_format($pr->getAdjustedHarvestingQuantity()) }} {{ __('view.global.stock') }}</td>
          <td class="text-right">{{ number_format($pr->producted_quantity) }} {{ __('view.global.stock') }}</td>
          <td class="text-right">{{ number_format($pr->getProductRate(), 2) }}&nbsp;%</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
