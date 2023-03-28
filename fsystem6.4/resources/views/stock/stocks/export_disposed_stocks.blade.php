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
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="font-size: 14">{{ __('view.stock.stocks.disposal_at') }}</td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="font-size: 14">{{ $group->disposal_at->format('Y/n/j') }}</td>
    </tr>
    <tr></tr>
    <tr>
      @foreach (range(1, 18) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="font-size: 26; border: 1px solid #000000;">
        {{ __('view.stock.stocks.disposal_stocks_file') }}
      </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>〒{{ $factory->postal_code }}</td>
    </tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>{{ $factory->address }}</td>
    </tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>{{ $factory->factory_name }}</td>
    </tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>TEL:&nbsp;{{ $factory->phone_number }}</td>
    </tr>
    <tr>
      @foreach (range(1, 40) as $column)
      <td></td>
      @endforeach
      <td>FAX:&nbsp;{{ $factory->fax_number }}</td>
    </tr>
    <tr></tr>
    <tr>
      <td></td>
      <td></td>
      <td>下記の通り廃棄しました。</td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
      <td></td>
      <td></td>
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.master.species.species') }}
      </td>
      @foreach (range(1, 5) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.master.factory_products.packaging_style') }}
      </td>
      @foreach (range(1, 11) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.global.base_weight') }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.shipment.global.harvesting_date') }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.stock.stocks.disposal_quantity') }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.stock.stocks.disposal_weight') }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ __('view.global.remark') }}
      </td>
    </tr>
    @foreach ($group->stocks as $s)
    <tr>
      <td></td>
      <td></td>
      <td valign="middle" style="border: 1px solid #000000;">
        {{ $s->species_name }}
      </td>
      @foreach (range(1, 5) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="border: 1px solid #000000;">
        {{ $s->number_of_heads }}{{ __('view.global.stock') }}
        {{ $s->weight_per_number_of_heads }}g
        {{ $input_group_list[$s->input_group] }}
      </td>
      @foreach (range(1, 11) as $column)
      <td></td>
      @endforeach
      <td align="right" valign="middle" style="border: 1px solid #000000;" data-format='#,##0"g"'>
        {{ $s->weight_per_number_of_heads }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td align="center" valign="middle" style="border: 1px solid #000000;">
        {{ $s->getHarvestingDate()->format('Y/m/d') }}
      </td>
      @foreach (range(1, 3) as $column)
      <td></td>
      @endforeach
      <td align="right" valign="middle" style="border: 1px solid #000000;" data-format="#,##0">
        {{ $s->disposal_quantity }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td align="right" valign="middle" style="border: 1px solid #000000;" data-format='#,##0"g"'>
        {{ $s->disposal_weight }}
      </td>
      @foreach (range(1, 2) as $column)
      <td></td>
      @endforeach
      <td valign="middle" style="border: 1px solid #000000;">
        {{ $s->disposal_remark }}
      </td>
    </tr>
    <tr></tr>
    @endforeach
  </table>
</body>
