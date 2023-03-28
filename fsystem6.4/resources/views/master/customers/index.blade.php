@extends('layout')

@section('title')
{{ __('view.master.customers.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.customers.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.customers.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.customers.customer_code') }}</th>
          <td class="col-md-3 col-sm-3 col-xs-3"><input class="form-control ime-inactive {{ has_error('customer_code') }}" maxlength="{{ $customer_code->getMaxLength() }}" name="customer_code" value="{{ old('customer_code', $params['customer_code'] ?? '') }}"></td>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.customers.customer_name') }}</th>
          <td class="col-md-5 col-sm-5 col-xs-5"><input class="form-control ime-active {{ has_error('customer_name') }}" maxlength="50" name="customer_name" value="{{ old('customer_name', $params['customer_name'] ?? '') }}"></td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.customers.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.customers.add') }}">
        <i class="fa fa-plus"></i> {{ __('view.global.add') }}
      </a>
      @endcanSave
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>

@if (count($params) !== 0)
<div class="row">
  <div class="col-md-3 col-sm-3 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p><span class="search-result-count">{{ $customers->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}</p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $customers->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.customers.index'))
          <th class="col-md-1 col-sm-1 col-xs-1">{{ __('view.global.delete') }}</th>
          @endcanSave
          <th class="col-md-3 col-sm-3 col-xs-3">@sortablelink('customer_code', __('view.master.customers.customer_code'))</th>
          <th class="col-md-7 col-sm-7 col-xs-7">@sortablelink('customer_name', __('view.master.customers.customer_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($customers as $c)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.customers.edit', $c->customer_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.customers.index'))
          <td>
          @if ($c->isDeletable())
            <delete-form route-action="{{ route('master.customers.delete', $c->customer_code) }}">
            </delete-form>
          @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $c->customer_code }}</td>
          <td class="text-left">{{ $c->customer_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
