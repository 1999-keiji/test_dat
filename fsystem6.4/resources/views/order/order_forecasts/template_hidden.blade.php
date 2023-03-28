<!DOCTYPE html>
<html lang={{ app()->getLocale() }}>
<head>
  <meta charset="UTF-8">
</head>
<body>
  <table>
    <thead>
      <tr></tr>
      <tr>
        <td>工場コード</td>
        <td>{{ $factory->factory_code }}</td>
      </tr>
      <tr></tr>
    </thead>
    <tbody>
      <tr>
        <td>日付</td>
        <td>工場取扱商品連番</td>
        <td>納入先コード</td>
        <td>取込前商品数</td>
        <td>商品数</td>
        <td>更新日時</td>
        <td>取込可否</td>
      </tr>
      @php ($row = $config['visible_forecast']['merge_start_row'])
      @foreach ($factory_products as $fp)
        @foreach ($fp->delivery_destinations as $dd)
          @foreach ($dd->delivery_dates as $idx => $delivery_date)
          <tr>
            <td>{{ $delivery_date['date']->format('Y/m/d') }}</td>
            <td>{{ $fp->factory_product_sequence_number }}</td>
            <td>{{ $dd->delivery_destination_code }}</td>
            <td>{{ $delivery_date['forecast'] }}</td>
            <td>
              ={{ $config['visible_forecast']['sheet_name_number'] }}!{{ get_excel_column_str($idx + $config['visible_forecast']['date_start_column']).$row }}
            </td>
            <td>{{ $delivery_date['updated_at'] }}</td>
            <td>{{ $delivery_date['can_import'] ? 1 : 0 }}</td>
          </tr>
          @endforeach
          @php ($row = $row + 1)
        @endforeach
      @endforeach
    </tbody>
  </table>
</body>
</html>
