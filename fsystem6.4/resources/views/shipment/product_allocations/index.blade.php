@extends('layout')

@section('title')
{{ __('view.shipment.product_allocations.index') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('shipment.productized_results.index') }}">{{ __('view.shipment.productized_results.index') }}</a>
  </li>
  <li>{{ __('view.shipment.product_allocations.index') }}</li>
@endsection

@section('content')
<allocate-products
  :factory="{{ $factory }}"
  :species="{{ $species }}"
  :packaging-styles="{{ json_encode($packaging_styles) }}"
  :warehouses="{{ $wareshouses }}"
  :input-group-list="{{ json_encode($input_group_list) }}"
  :shipping-dates="{{ json_encode($shipping_dates) }}"
  :selected-packaging-style="{{ json_encode($packaging_style ?: new \stdClass()) }}"
  selected-warehouse-code="{{ request('warehouse_code', $factory->getDefaultWarehouse()->warehouse_code) }}"
  :factory-products="{{ json_encode($factory_products) }}"
  :warning-date-term-of-allocation="{{ json_encode($warning_date_term_of_allocation) }}"
  href-to-index-of-productized-results="{{ route('shipment.productized_results.index') }}"
  :can-save-product-allocation="{{ json_encode(Auth::user()->canSave(route_relatively('shipment.productized_results.index'))) }}">
</allocate-products>
@endsection
