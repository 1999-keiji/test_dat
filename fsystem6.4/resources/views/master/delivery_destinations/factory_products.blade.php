@extends('layout')

@section('title')
{{ __('view.master.delivery_destinations.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.delivery_destinations.index') }}">{{ __('view.master.delivery_destinations.index') }}</a>
  </li>
  <li>{{ __('view.master.delivery_destinations.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.delivery_destinations.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li><a href="{{ route('master.delivery_destinations.edit', $delivery_destination->delivery_destination_code) }}">{{ __('view.master.global.base_data') }}</a></li>
  <li><a href="{{ route('master.delivery_destinations.warehouses', $delivery_destination->delivery_destination_code) }}">{{ __('view.master.warehouses.warehouse') }}</a></li>
  <li class="active"><a href="#product_tab">{{ __('view.master.products.product') }}</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="product_tab">
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-6 col-sm-offset-6 col-xs-offset-6">
        @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
        <add-delivery-factory-product
          route-action="{{ route('master.delivery_factory_products.create', $delivery_destination->delivery_destination_code) }}"
          delivery-destination-code="{{ $delivery_destination->delivery_destination_code }}"
          :factories="{{ $factories }}"
          :currencies="{{ $currencies }}"
          :unit-price="{{ $unit_price->toJson() }}">
        </add-delivery-factory-product>
        @endcanSave
      </div>
    </div>
    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
        <p>
          <span class="search-result-count">{{ count($delivery_factory_products) }}</span>{{ __('view.global.suffix_serach_result_count') }}
        </p>
      </div>
      <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
        {{ $delivery_factory_products->appends(request(['sort', 'order']))->links() }}
      </div>
    </div>
    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <thead>
            <tr>
              @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
              <th>{{ __('view.global.edit') }}</th>
              <th>{{ __('view.global.delete') }}</th>
              @endcanSave
              <th>{{ __('view.master.factories.factory') }}</th>
              <th>{{ __('view.master.products.product') }}</th>
              <th>{{ __('view.master.factory_products.factory_product') }}</th>
              <th>{{ __('view.master.global.application_started_on') }}</th>
              <th>{{ __('view.master.global.application_ended_on') }}</th>
              <th>{{ __('view.master.global.unit_price') }}</th>
              <th>{{ __('view.master.global.currency_code') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($delivery_factory_products as $dfp)
              <tr>
                @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
                <td rowspan="{{ $dfp->factory_product_special_prices->getRowspan() }}">
                  <edit-delivery-factory-product
                    route-action="{{ route('master.delivery_factory_products.update', $dfp->getJoinedPrimaryKeys()) }}"
                    :delivery-factory-product="{{ $dfp }}"
                    :current-factory-product-speciel-prices="{{ $dfp->factory_product_special_prices->toJson() }}"
                    :currencies="{{ $currencies }}"
                    :unit-price="{{ $unit_price->toJson() }}">
                  </edit-delivery-factory-product>
                </td>
                <td rowspan="{{ $dfp->factory_product_special_prices->getRowspan() }}">
                  <delete-form route-action="{{ route('master.delivery_factory_products.delete', $dfp->getJoinedPrimaryKeys()) }}">
                  </delete-form>
                </td>
                @endcanSave
                <td class="text-left" rowspan="{{ $dfp->factory_product_special_prices->getRowspan() }}">{{ $dfp->factory_abbreviation }}</td>
                <td class="text-left" rowspan="{{ $dfp->factory_product_special_prices->getRowspan() }}">{{ $dfp->product_code }}:&nbsp;{{ $dfp->product_name }}</td>
                <td class="text-left" rowspan="{{ $dfp->factory_product_special_prices->getRowspan() }}">{{ $dfp->factory_product_name }}</td>
                <td>{{ optional($dfp->factory_product_special_prices->first())->application_started_on ?: '-' }}</td>
                <td>{{ optional($dfp->factory_product_special_prices->first())->application_ended_on ?: '-' }}</td>
                <td class="{{ $dfp->factory_product_special_prices->isNotEmpty() ? 'text-right' : '' }}">
                  @if ($dfp->factory_product_special_prices->first())
                  {{ $dfp->factory_product_special_prices->first()->unit_price->format() }}
                  @else
                  -
                  @endif
                </td>
                <td>{{ optional($dfp->factory_product_special_prices->first())->currency_code ?: '-' }}</td>
              </tr>
              @foreach ($dfp->factory_product_special_prices->exceptFirst() as $fpsp)
              <tr>
                <td>{{ $fpsp->application_started_on }}</td>
                <td>{{ $fpsp->application_ended_on }}</td>
                <td class="text-right">{{ $fpsp->unit_price->format() }}</td>
                <td>{{ $fpsp->currency_code }}</td>
              </tr>
              @endforeach
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
