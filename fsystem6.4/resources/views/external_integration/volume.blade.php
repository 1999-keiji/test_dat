@extends('layout')

@section('title')
{{ __('view.stock.stocks.index') }}
@endsection

@section('menu-section')
{{ __('view.index.external_integration') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.external_integration.jccores') }}</li>
@endsection

@section('content')

<div class="container">
  <h2>テストtt</h2>

  <table class="table">
    <thead>
      <tr>
        <th scope="col">APタイプ</th>
        <th scope="col">データ種別</th>
        <th scope="col">年度</th>
        <th scope="col">年月</th>
        <th scope="col">会計単位</th>
        <th scope="col">事務所</th>
        <th scope="col">原価部門</th>
        <th scope="col">原価規格</th>
        <th scope="col">生産量</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $i)
        <tr>
          <td>{{ $i['AP_type'] }}</td>
          <td>{{ $i['data_species'] }}</td>
          <td>{{ $i['year'] }}</td>
          <td>{{ $i['year_month'] }}</td>
          <td>{{ $i['account_unit'] }}</td>
          <td>{{ $i['office'] }}</td>
          <td>{{ $i['status'] }}</td>
          <td>{{ $i['product_type'] }}</td>
          <td>{{ $i['production_quantity'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>



@endsection