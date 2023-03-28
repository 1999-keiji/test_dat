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
<div>
  <form action="{{ route('external_integration.jccores.search') }}" id="search-jccores-form" class="form-horizontal basic-form" method="POST">
    <div class="row">
      <search-jccores-form
      :factories="{{ $factories }}"
      :search-params="{{ json_encode($params ?: new \stdClass()) }}"
      :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
      :errors="{{ $errors }}"
      ></search-jccores-form>
      <div class="col-md-1 col-sm-1 col-xs-1">
        <button class="btn btn-lg btn-default pull-left" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
        </button>
      </div>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
  <!-- 検索結果表示 -->
  @if(count($params) !== 0)
  <div class="row">
    <div class="col-md-9 col-sm-9 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <div class="" style="text-align:right;">
        <label for="">一括ダウンロード</label>
        <a type="button" class="" href="{{ route('external_integration.jccores.zip') }}" style="background-color: #696969; color:#fff; border-radius: 4px; padding:2px 16px; touch-action:manipulation; cursor: pointer;">zip</a>
      </div>
      <div class="col-md-8 col-sm-8 col-xs-9" style="float:right; padding:0;">
        <table class="table table-color-bordered">
          <thead style="background-color: #d7e4bd;">
            <tr>
              <th scope="col">対象期間</th>
              <th scope="col">データ内容</th>
              <th scope="col">ファイル出力</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <tr>
              <td>{{ $params['date_month'] }}</td>
              <td>生産量登録</td>
              <td><button class=""> <a href="{{ route('external_integration.jccores.volume', ['session' => $params]) }}" style="color: #000; text-decoration:node;">dat出力</a></button></td>
            </tr>
            <tr>
              <td>{{ $params['date_month'] }}</td>
              <td>受払登録</td>
              <td><button class=""> <a href="{{ route('external_integration.jccores.consumption', ['session' => $params]) }}" style="color: #000; text-decoration:node;">dat出力</a></button></td>
            </tr>
            <tr>
              <td>{{ $params['date_month'] }}</td>
              <td>消費量登録</td>
              <td><button class=""> <a href="{{ route('external_integration.jccores.receipt', ['session' => $params]) }}" style="color: #000; text-decoration:node;">dat出力</a></button></td>
            </tr>
          </tbody>
        </table>
      </div>
      
    </div>
  </div>
  @endif
  <!-- 検索結果表示 -->
</div>

@endsection
