<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $style_header = 'background-color: #C5D9F1; border: 1px solid #000000;';
    $style = 'border: 1px solid #000000;';
    $expired_style = 'background-color: #FFBBFF; border: 1px solid #000000;';
  @endphp
</head>
<body>
  <table>
    <tr>
      <th colspan="3" height="30" align="center" valign="middle" style="font-size: 50;">
        {{ __('view.stock.stock_histories.index') }}
        【{{ $factory->factory_abbreviation }}&nbsp;{{ $params['working_date_from'] }}&nbsp;～&nbsp;{{ $params['working_date_to'] }}】
      </th>
    </tr>
    <tr>
      <th width="20" align="center" style="{{ $style_header }}">操作日</th>
      <th width="20" align="center" style="{{ $style_header }}">画面</th>
      <th width="20" align="center" style="{{ $style_header }}">保管</th>
      <th width="10" align="center" style="{{ $style_header }}">状態</th>
      <th width="20" align="center" style="{{ $style_header }}">品種</th>
      <th width="20" align="center" style="{{ $style_header }}">商品規格</th>
      <th width="20" align="center" style="{{ $style_header }}">収穫日</th>
      <th width="20" align="center" style="{{ $style_header }}">有効期限(参考)</th>
      <th width="20" align="center" style="{{ $style_header }}">納入日</th>
      <th width="10" align="center" style="{{ $style_header }}">引当</th>
      <th width="20" align="center" style="{{ $style_header }}">納入先</th>
      <th width="15" align="center" style="{{ $style_header }}">配送LT</th>
      <th width="15" align="center" style="{{ $style_header }}">数量</th>
      <th width="15" align="center" style="{{ $style_header }}">遷移</th>
      <th width="15" align="center" style="{{ $style_header }}">重量(kg)</th>
      <th width="20" align="center" style="{{ $style_header }}">作業者</th>
    </tr>
    @foreach ($stock_histories as $sh)
    <tr>
      <td style="{{ $style }}">{{ $sh->created_at }}</td>
      <td style="{{ $style }}">{{ $sh->screen }}</td>
      <td style="{{ $style }}">{{ $sh->warehouse_abbreviation }}</td>
      <td style="{{ $style }}">{{ array_flip($stock_status_list)[$sh->stock_status] }}</td>
      <td style="{{ $style }}">{{ $sh->species_name }}</td>
      <td style="{{ $style }}">
        {{ $sh->number_of_heads }}株
        {{ $sh->weight_per_number_of_heads }}g
        {{ $input_group_list[$sh->input_group] }}
      </td>
      <td style="{{ $style }}">{{ $sh->harvesting_date }}</td>
      <td style="{{ $style }}">{{ $sh->expiration_date }}</td>
      <td style="{{ $style }}">{{ $sh->delivery_date }}</td>
      <td style="{{ $style }}">{{ array_flip($allocation_status_list)[$sh->allocation_flag] }}</td>
      <td style="{{ $style }}">{{ $sh->delivery_destination_abbreviation }}</td>
      <td style="{{ $style }}">
        @if ($sh->delivery_lead_time)
        {{ $sh->delivery_lead_time }}日
        @endif
      </td>
      <td style="{{ $style }}">{{ $sh->stock_quantity }}</td>
      <td style="{{ $style }}">{{ $sh->transistion_quantity }}</td>
      <td style="{{ $style }}" data-format='#,##0.0'>
        {{ $sh->stock_quantity * $sh->weight_per_number_of_heads }}
      </td>
      <td style="{{ $style }}">{{ $sh->user_name }}</td>
    </tr>
    @endforeach
  </table>
</body>
</html>
