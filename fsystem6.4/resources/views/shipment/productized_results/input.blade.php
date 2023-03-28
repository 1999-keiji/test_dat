@extends('layout')

@section('title')
{{ __('view.shipment.productized_results.input') }}
@endsection

@section('menu-section')
{{ __('view.index.shipment') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('shipment.productized_results.index') }}">
      {{ __('view.shipment.productized_results.index') }}
    </a>
  </li>
  <li>{{ __('view.shipment.productized_results.input') }}</li>
@endsection

@section('content')
<input-productized-results
  :factory="{{ $factory }}"
  :species="{{ $species }}"
  :harvesting-date="{{ json_encode($harvesting_date) }}"
  :productized-result="{{ $productized_result }}"
  :productized-result-details="{{ $productized_result_details }}"
  :species-average-weight="{{ json_encode($species_average_weight) }}"
  :input-group-list="{{ json_encode($input_group_list) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}"
  href-to-index="{{ route('shipment.productized_results.index') }}"
  :can-save-productized-result="{{ json_encode(Auth::user()->canSave(route_relatively('shipment.productized_results.index'))) }}">
</input-productized-results>
@endsection
