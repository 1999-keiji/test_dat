<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
        <td>品種コード</td>
        <td>{{ $species->species_code }}</td>
      </tr>
      <tr></tr>
    </thead>
    <tbody>
      <tr>
        <td>収穫日</td>
        <td>取込前予想製品化率</td>
        <td>予想製品化率</td>
        <td>取込前収穫廃棄</td>
        <td>収穫廃棄</td>
        <td>取込前前採り</td>
        <td>前採り</td>
        <td>更新日</td>
      </tr>
      @foreach ($forecasted_product_rates as $idx => $fpr)
        <tr>
          <td>{{ $fpr['date'] }}</td>
          <td>{{ $fpr['product_rate'] }}</td>
          <td>
            =IF(
              {{ $config['visible_sheet']['sheet_title'] }}!E{{ $fpr['referring_row'] }}="","",{{ $config['visible_sheet']['sheet_title'] }}!E{{ $fpr['referring_row'] }}*100
            )
          </td>
          <td>{{ $fpr['crop_failure'] }}</td>
          <td>
            =IF(
              {{ $config['visible_sheet']['sheet_title'] }}!{{ get_excel_column_str($fpr['pack_number_set_num'] + 5).$fpr['referring_row'] }}="","",{{ $config['visible_sheet']['sheet_title'] }}!{{ get_excel_column_str($fpr['pack_number_set_num'] + 5).$fpr['referring_row'] }}
            )
          </td>
          <td>{{ $fpr['advanced_harvest'] }}</td>
          <td>
            =IF(
              {{ $config['visible_sheet']['sheet_title'] }}!{{ get_excel_column_str($fpr['pack_number_set_num'] + 6).$fpr['referring_row'] }}="","",{{ $config['visible_sheet']['sheet_title'] }}!{{ get_excel_column_str($fpr['pack_number_set_num'] + 6).$fpr['referring_row'] }}
            )
          </td>
          <td>{{ $fpr['updated_at'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
