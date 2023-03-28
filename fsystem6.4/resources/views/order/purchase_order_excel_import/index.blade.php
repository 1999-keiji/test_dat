@extends('layout')

@section('title')
{{ __('view.order.purchase_order_excel_import.index') }}
@endsection

@section('menu-section')
{{ __('view.index.order') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.order.purchase_order_excel_import.index') }}</li>
@endsection

@section('content')
@if (session('error_messages'))
<div class="row">
  <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
    <div class="alert alert-warning">
      <ul>
        @foreach (session('error_messages') as $idx => $messages)
          @foreach ($messages as $message)
          <li>{{ $idx }}行目: {{ $message }}</li>
          @endforeach
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endif

<div class="row">
  <form id="purchase-order-excel-import-form" class="form-horizontal basic-form import-data-form" action="{{ route('order.purchase_order_excel_import.import') }}" method="POST" enctype="multipart/form-data">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>
            {{ __('view.master.factories.factory') }}
            <span class="required-mark">*</span>
          </th>
          <td>
            <select class="form-control" name="factory_code">
              <option value=""></option>
              @foreach ($factories as $f)
                <option value="{{ $f->factory_code }}" {{ is_selected($f->factory_code, old('factory_code')) }}>{{ $f->factory_abbreviation }}</option>
              @endforeach
            </select>
          </td>
          <th>
            {{ __('view.master.customers.customer') }}
            <span class="required-mark">*</span>
          </th>
          <td>
            <select class="form-control" name="customer_code">
              <option value=""></option>
              @foreach ($customers as $c)
                <option value="{{ $c->customer_code }}" {{ is_selected($c->customer_code, old('customer_code')) }}>{{ $c->customer_abbreviation }}</option>
              @endforeach
            </select>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tbody>
          <tr>
            <th>
              {{ __('view.global.file') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <input type="text" id="file_name_view" name="file_name_view" disabled>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-1 col-sm-1 col-xs-1">
      <input id="file_select_hidden" name="import_file" type="file">
      <button id="file_select_btn" class="btn btn-lg btn-default">{{ __('view.global.refer') }}</button>
    </div>
    @canSave(Auth::user(), route_relatively('order.purchase_order_excel_import.index'))
    <div class="col-md-3 col-sm-3 col-xs-3">
      <button class="btn btn-lg btn-default pull-right import-data" type="button">
        <i class="fa fa-upload"></i> {{ __('view.global.excel_upload') }}
      </button>
    </div>
    @endcanSave
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>
@endsection
