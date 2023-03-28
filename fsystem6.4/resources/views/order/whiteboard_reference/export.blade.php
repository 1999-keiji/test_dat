<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
</head>
<body>
  @php ($header_bg_color = '#d7e4bd')
  @php ($border = '1px solid #000000')
  @php ($border_double = '1px double #000000')
  @php ($bg_colors = ['only_fixed_order' => '#ffd900', 'order_quantity_updated' => '#ff0000', 'collection_time_updated' => '#f09199'])
  <table class="table table-color-bordered">
    <thead>
      <tr>
        <th align="left">
          {{ $year_month->format('Y/m') }}&nbsp;-
          {{ $factory->factory_abbreviation }}&nbsp;-
          {{ $species->species_abbreviation }}&nbsp;-
          {{ ($year_month instanceof \App\ValueObjects\Date\ShippingDate) ? __('view.shipment.global.shipping_date') : __('view.shipment.global.delivery_date') }}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td width="14" style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.global.packaging') }}
        </td>
        @foreach ($packaging_styles as $ps)
        <td
          colspan="{{ $ps->count_of_delivery_destination }}"
          align="center"
          style="border-top:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};
            border-left:@if (!$loop->first) {{ $border_double }} @else {{ $border }} @endif;">
            {{ $ps->weight_per_number_of_heads }}{{ __('view.global.gram') }}
        </td>
        @endforeach
      </tr>
      <tr>
        <td style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.global.quantity_per_case') }}
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
          <td
            colspan="{{ count($number_of_cases->delivery_destinations) }}"
            align="center"
            style="border-top:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};
              border-left:@if (!$loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;">
              {{ $number_of_cases->number_of_cases }}P
          </td>
          @endforeach
        @endforeach
      </tr>
      <tr height="170">
        <td valign="middle" style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.master.delivery_destinations.delivery_destination') }}
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
            @foreach ($number_of_cases->delivery_destinations as $dd)
            <td
              width="8"
              align="center"
              valign="middle"
              style="border-top:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};
                border-left:@if (!$loop->parent->parent->first && $loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;">
              {!! implode("<br>", explode(' ', mb_convert_kana($dd->delivery_destination_abbreviation, 'AKVs'))) !!}
            </td>
            @endforeach
          @endforeach
        @endforeach
      </tr>
      <tr>
        <td style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.order.whiteboard_reference.transport_company') }}
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
            @foreach ($number_of_cases->delivery_destinations as $dd)
            <td
              style="border-top:{{ $border }};border-right:{{ $border }};
                border-left:@if (!$loop->parent->parent->first && $loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;">
              {{ $dd->transport_company_abbreviation }}
            </td>
            @endforeach
          @endforeach
        @endforeach
      </tr>
      <tr>
        <td style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.master.transport_companies.collection_time') }}
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
            @foreach ($number_of_cases->delivery_destinations as $dd)
            <td
              style="border-top:{{ $border }};border-right:{{ $border }};
                border-left:@if (!$loop->parent->parent->first && $loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;">
              {{ $dd->collection_time }}
            </td>
            @endforeach
          @endforeach
        @endforeach
      </tr>
      <tr>
        <td style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ __('view.order.whiteboard_reference.delivery_lead_time') }}
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
            @foreach ($number_of_cases->delivery_destinations as $dd)
            <td
              style="border-top:{{ $border }};border-right:{{ $border }};
                border-left:@if (!$loop->parent->parent->first && $loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;">
              {{ $dd->delivery_lead_time->toJapaneseSyntax() }}
            </td>
            @endforeach
          @endforeach
        @endforeach
      </tr>
      @foreach ($dates as $date)
      <tr>
        @php ($idx = 0)
        <td
          width="10"
          style="border-top:{{ $border }};border-left:{{ $border }};border-right:{{ $border }};border-bottom:{{ $border }};background-color:{{ $header_bg_color }};">
          {{ $date['date']->format('m/d') }}
          <span style="color:{{ $date['date']->getDayOfWeekColor() }}">（{{ $date['date']->dayOfWeekJa() }}）</span>
        </td>
        @foreach ($packaging_styles as $ps)
          @foreach ($ps->list_of_number_of_cases as $number_of_cases)
            @foreach ($number_of_cases->delivery_destinations as $dd)
              @php ($order_quantity = $date['order_quantities'][$idx])
              <td
                align="right"
                style="border-top:{{ $border }};border-right:{{ $border }};border-bottom:{{ $border }};
                  border-left:@if (!$loop->parent->parent->first && $loop->parent->first && $loop->first) {{ $border_double }} @else {{ $border }} @endif;
                  background-color:{{ $bg_colors[$order_quantity['status']] ?? '#ffffff' }}">
                @if ($order_quantity['shipping'] === 'allocated')
                ☆
                @endif
                @if ($order_quantity['shipping'] === 'shipped')
                ★
                @endif
                <span @if ($order_quantity['type'] === 'order') style="font-weight:bold;" @endif>
                  {{ $order_quantity['quantity'] }}
                </span>
              </td>
              @php ($idx += 1)
            @endforeach
          @endforeach
        @endforeach
      </tr>
      @endforeach
    </tbody>
  <table>
</body>
</html>
