<!DOCTYPE html>
<html>
  <meta charset="UTF-8">
</html>
<body>
  <table>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
      @foreach (range(1, 44) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="font-size: 14">{{ $working_date->format('Y/n/j') }}</td>
    </tr>
    <tr></tr>
    <tr>
      @foreach (range(1, 18) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="font-size: 26; border: 1px solid #000000;">
        {{ __('view.stock.stocks.moved_stock_file') }}
      </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
      <td></td>
      <th>{{ __('view.stock.stocks.moving_to') }}</th>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <th>{{ __('view.stock.stocks.moving_from') }}</th>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>〒{{ $stock->warehouse->postal_code }}</td>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <td>〒{{ $stock->departure_warehouse->postal_code }}</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td valign="middle" style="font-size: 16">{{ $stock->warehouse->address }}</td>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <td>{{ $stock->departure_warehouse->address }}</td>
    </tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>{{ $stock->factory->factory_name }}</td>
    </tr>
    <tr>
      <td height="16"></td>
      <td></td>
      <td style="font-size: 14;">{{ $stock->warehouse->warehouse_name }}</td>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <td>{{ $stock->departure_warehouse->warehouse_name }}</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>TEL:&nbsp;{{ $stock->warehouse->phone_number }}</td>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <td>TEL:&nbsp;{{ $stock->departure_warehouse->phone_number }}</td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>FAX:&nbsp;{{ $stock->warehouse->fax_number }}</td>
      @foreach (range(1, 37) as $column)
      <td></td>
      @endforeach
      <td>FAX:&nbsp;{{ $stock->departure_warehouse->fax_number }}</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
      <td></td>
      <td></td>
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.master.species.species') }}
      </td>
      @foreach (range(1, 8) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.master.factory_products.packaging_style') }}
      </td>
      @foreach (range(1, 11) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.shipment.global.delivery_date') }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.stock.stocks.moving_quantity') }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.global.remark') }}
      </td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td></td>
      <td valign="middle" style="border: 1px solid #000000;">
        {{ $stock->species->species_name }}
      </td>
      @foreach (range(1, 8) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="border: 1px solid #000000;">
        {{ $stock->number_of_heads }}{{ __('view.global.stock') }}
        {{ $stock->weight_per_number_of_heads }}g
        {{ $input_group_list[$stock->input_group] }}
      </td>
      @foreach (range(1, 11) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ $working_date->parse($stock->moving_complete_at)->format('n/j') }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td align="right" valign="middle" style="border: 1px solid #000000;" data-format="#,##0">
        {{ $stock->stock_quantity }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="border: 1px solid #000000;"></td>
    </tr>
  </table>
</body>
