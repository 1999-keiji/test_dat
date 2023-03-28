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
        <td>基本入り株数</td>
        <td>基本入り数あたり重量</td>
        <td>出来高入力グループ</td>
        <td>取込前出来高数</td>
        <td>出来高数</td>
        <td>更新日時</td>
      </tr>
      @foreach ($crops as $c)
        <tr>
          <td>{{ $c['date'] }}</td>
          <td>{{ $c['number_of_heads'] }}</td>
          <td>{{ $c['weight_per_number_of_heads'] }}</td>
          <td>{{ $c['input_group'] }}</td>
          <td>{{ $c['crop_number'] }}</td>
          <td>
            =IF(
              {{ $config['visible_sheet']['sheet_title'] }}!{{ $c['refrrring_cell'] }}="","",{{ $config['visible_sheet']['sheet_title'] }}!{{ $c['refrrring_cell'] }}
            )
          </td>
          <td>{{ $c['updated_at'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
