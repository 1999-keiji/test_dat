@extends('layout')

@section('title')
{{ __('view.order.whiteboard_reference.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.whiteboard_reference.index') }}</li>
@endsection

@section('styles')
  @parent

  <style type="text/css">
    th.transport_company_name, td.transport_company_name {
      height: 4em;
    }
    td.order-quantity {
      padding-left: 5px;
      padding-right: 5px;
    }
  </style>
@endsection

@section('content')
<whiteboard-reference
  :factories="{{ $factories }}"
  :output-condition-list="{{ json_encode($output_condition_list->all()) }}"
  :search-params="{{ json_encode($params ?: new \stdClass()) }}"
  :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
  :errors="{{ $errors }}"
  :reload-interval="{{ json_encode(config('settings.order.whiteboard_reference.reload_interval')) }}"
  search-action="{{ route('order.whiteboard_reference.search') }}"
  export-action="{{ route('order.whiteboard_reference.export') }}">
</whiteboard-reference>

@if (count($params) !== 0)
<div id="whiteboard-reference-list">
  <div id="factory-products-item"  class="row">
    <div id="factory-products-header" class="col-md-2 col-sm-2 col-xs-3">
      <table class="table table-color-bordered">
        <thead>
          <tr>
            <th>{{ __('view.global.packaging') }}</th>
          </tr>
          <tr>
            <th>{{ __('view.global.quantity_per_case') }}</th>
          </tr>
          <tr>
            <th class="header-delivery-destination">
              {{ __('view.master.delivery_destinations.delivery_destination') }}
            </th>
          </tr>
        </thead>
      </table>
    </div>
    <div id="factory-products-table">
      <table class="table table-color-bordered">
        <colgroup>
          @foreach ($packaging_styles as $ps)
            @foreach ($ps->list_of_number_of_cases as $number_of_cases)
              @foreach ($number_of_cases->delivery_destinations as $dd)
              <col class="table-cols">
              @endforeach
            @endforeach
          @endforeach
        </colgroup>
        <thead>
          <tr>
            @foreach ($packaging_styles as $ps)
            <th
              class="@if(! $loop->first) border-left-double @endif"
              colspan="{{ $ps->count_of_delivery_destination }}">
              {{ $ps->weight_per_number_of_heads }}{{ __('view.global.gram') }}
            </th>
            @endforeach
          </tr>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
              <th
                class="@if(! $loop->parent->first && $loop->first) border-left-double @endif"
                colspan="{{ count($number_of_cases->delivery_destinations) }}">
                {{ $number_of_cases->number_of_cases }}P
              </th>
              @endforeach
            @endforeach
          </tr>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                <th
                  class="align-top header-delivery-destination-name
                  @if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                  @foreach (array_reverse(explode(' ', mb_convert_kana($dd->delivery_destination_abbreviation, 'AKVs'))) as $exploded)
                  <p class="delivery-destination-name">{{ $exploded }}</p>
                  @endforeach
                </th>
                @endforeach
              @endforeach
            @endforeach
          </tr>
        </thead>
      </table>
    </div>
  </div>

  <div id="collection-time-item" class="row">
    <div id="collection-time-header" class="col-md-2 col-sm-2 col-xs-3">
      <table class="table table-color-bordered">
        <thead>
          <tr>
            <th class="transport_company_name">{{ __('view.order.whiteboard_reference.transport_company') }}</th>
          </tr>
          <tr>
            <th>{{ __('view.master.transport_companies.collection_time') }}</th>
          </tr>
          <tr>
            <th>{{ __('view.order.whiteboard_reference.delivery_lead_time') }}</th>
          </tr>
          <tr>
            <th>{{ __('view.master.global.note') }}</th>
          </tr>
          @foreach ($dates as $date)
          <tr>
            <th>
              {{ $date['date']->format('n/j') }}
              <span style="color: {{ $date['date']->getDayOfWeekColor() }}">（{{ $date['date']->dayOfWeekJa() }}）</span>
            </th>
          </tr>
          @endforeach
        </thead>
      </table>
    </div>

    <div id="collection-time-table">
      <table class="table table-color-bordered">
        <colgroup>
          @foreach ($packaging_styles as $ps)
            @foreach ($ps->list_of_number_of_cases as $number_of_cases)
              @foreach ($number_of_cases->delivery_destinations as $dd)
              <col class="table-cols">
              @endforeach
            @endforeach
          @endforeach
        </colgroup>
        <tbody>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                <td
                  class="text-left transport_company_name
                  @if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                  {{ $dd->transport_company_abbreviation ?: '&nbsp;' }}
                </td>
                @endforeach
              @endforeach
            @endforeach
          </tr>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                <td
                  class="collection-time text-left
                  @if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                  {{ $dd->collection_time ?: '&nbsp;' }}
                </td>
                @endforeach
              @endforeach
            @endforeach
          </tr>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                <td
                  class="text-left
                  @if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                  {{ $dd->delivery_lead_time->toJapaneseSyntax() }}
                </td>
                @endforeach
              @endforeach
            @endforeach
          </tr>
          <tr>
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                <td class="@if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                  @if ($dd->note)
                    <i class="fa fa-exclamation-triangle fa-tooltip text-warning" data-toggle="tooltip" title="{{ $dd->note }}"></i>
                  @else
                  &nbsp;
                  @endif
                </td>
                @endforeach
              @endforeach
            @endforeach
          </tr>
          @foreach ($dates as $date)
          <tr>
            @php ($idx = 0)
            @foreach ($packaging_styles as $ps)
              @foreach ($ps->list_of_number_of_cases as $number_of_cases)
                @foreach ($number_of_cases->delivery_destinations as $dd)
                  @php ($order_quantity = $date['order_quantities'][$idx])
                  <td class="
                    {{ $order_quantity['status'] }}
                    @if (! $loop->parent->parent->first && $loop->parent->first && $loop->first) border-left-double @endif">
                    <table class="table-quantity">
                      <tbody>
                        <tr>
                          <td class="col-md-4 col-sm-4 col-xs-4">&nbsp;</td>
                          <td class="col-md-4 col-sm-4 col-xs-4">
                            @if ($order_quantity['shipping'] === 'allocated')
                            <i class="fa fa-star-o"></i>
                            @endif
                            @if ($order_quantity['shipping'] === 'shipped')
                            <i class="fa fa-star"></i>
                            @endif
                          </td>
                          <td class="col-md-4 col-sm-4 col-xs-4 order-quantity {{ $order_quantity['type'] }}">
                            <span
                              @if ($order_quantity['per_collection_time'])
                              data-toggle="tooltip"
                              title="{{ $order_quantity['per_collection_time'] }}"
                              @endif>
                              {{ $order_quantity['quantity'] }}
                            </span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  @php ($idx += 1)
                @endforeach
              @endforeach
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    if ($('#whiteboard-reference-list').length) {
      $('#whiteboard-reference-list th.header-delivery-destination').outerHeight(
        $('#whiteboard-reference-list th.header-delivery-destination-name').outerHeight()
      )
      $('#collection-time-table').scroll(function () {
        $('#factory-products-table').scrollLeft($(this).scrollLeft())
        $('#collection-time-header').scrollTop($(this).scrollTop())
      })

      $(window).on('resize', function () {
        var min_height = $('#factory-products-table').outerHeight()
        var table_height = $(window).height() - ($('#collection-time-item').offset().top + 20)
        $('#collection-time-item').outerHeight(Math.max(table_height, min_height))
        $('#factory-products-table').outerWidth($('#collection-time-table').get(0).clientWidth)
        $('#collection-time-header').outerHeight($('#collection-time-table').get(0).clientHeight)
      }).trigger('resize')

      $('#whiteboard-reference-list').css('visibility', 'visible')
    }
  </script>
@endsection
