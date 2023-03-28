<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $order_status = [
      'all' => 'すべて',
      'temporary' => '仮注文',
      'fixed' => '確定済',
      'cancel' => 'キャンセル',
      'slip' => '赤伝黒伝'
    ];
  @endphp
</head>
<body>
  <table>
    <tr>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">工場</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">ステータス</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">エンドユーザ</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納入先</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">注文日FROM</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">注文日TO</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納入日FROM</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納入日To</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">注文番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">BASE+注文番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">引当状態</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">出荷状態</th>
    </tr>
    <tr>
      <td style="border: 1px solid #000000;">{{ $factory->factory_abbreviation }}</td>
      <td style="border: 1px solid #000000;">{{ $customer->customer_abbreviation }}</td>
      <td style="border: 1px solid #000000;">{{ $order_status[$params['order_status']] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['end_user_name'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['delivery_destination_name'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['received_date_from'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['received_date_to'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['delivery_date_from'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['delivery_date_to'] }}</td>
      <td style="border: 1px solid #000000;">{{ $params['order_number'] }}</td>
      <td style="border: 1px solid #000000;">
        @if ($params['base_plus_order_number'] && $params['base_plus_order_chapter_number'])
        {{ $params['base_plus_order_number'] }}-{{ $params['base_plus_order_chapter_number'] }}
        @endif
      </td>
      <td style="border: 1px solid #000000;">{{ array_flip($allocation_status->all())[$params['allocation_status']] ?? '' }}</td>
      <td style="border: 1px solid #000000;">{{ array_flip($shipment_status->all())[$params['shipment_status'] ?? null] ?? '' }}</td>
    </tr>
  </table>
</body>
</html>
