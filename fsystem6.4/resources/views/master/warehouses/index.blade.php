@extends('layout')

@section('title')
{{ __('view.master.warehouses.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.warehouses.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.warehouses.search') }}" method="POST">
    <div class="col-md-6 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.warehouses.warehouse_code') }}</th>
          <td>
            <input class="form-control ime-inactive {{ has_error('warehouse_code') }}" maxlength="{{ $warehouse_code->getMaxLength() }}" name="warehouse_code" value="{{ old('warehouse_code', $params['warehouse_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.warehouses.warehouse_name') }}</th>
          <td>
            <input class="form-control ime-active {{ has_error('warehouse_name') }}" maxlength="50" name="warehouse_name" value="{{ old('warehouse_name', $params['warehouse_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-2 col-sm-offset-2">
      @canSave(Auth::user(), route_relatively('master.warehouses.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.warehouses.add') }}">
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

@if (count($params))
<div class="row">
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $warehouses->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-4 col-sm-4 col-md-offset-2 col-sm-offset-2">
    {{ $warehouses->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-7 col-sm-8 col-xs-10 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.warehouses.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('warehouse_code', __('view.master.warehouses.warehouse_code'))</th>
          <th>@sortablelink('warehouse_name', __('view.master.warehouses.warehouse_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($warehouses as $w)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.warehouses.edit', $w->warehouse_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.warehouses.index'))
          <td>
            @if ($w->isDeletable())
            <delete-form route-action="{{ route('master.warehouses.delete', $w->warehouse_code) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $w->warehouse_code }}</td>
          <td class="text-left">{{ $w->warehouse_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
