@extends('layout')

@section('title')
{{ __('view.master.delivery_destinations.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.delivery_destinations.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.delivery_destinations.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.delivery_destinations.delivery_destination_code') }}</th>
          <td>
            <input class="form-control ime-inactive {{ has_error('delivery_destination_code') }}" maxlength="{{ $delivery_destination_code->getMaxLength() }}" name="delivery_destination_code" value="{{ old('delivery_destination_code', $params['delivery_destination_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.delivery_destinations.index_delivery_destination_name') }}</th>
          <td>
            <input class="form-control ime-active {{ has_error('delivery_destination_name') }}" maxlength="50" name="delivery_destination_name" value="{{ old('delivery_destination_name', $params['delivery_destination_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.delivery_destinations.add') }}">
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
      <span class="search-result-count">{{ $delivery_destinations->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $delivery_destinations->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('delivery_destination_code', __('view.master.delivery_destinations.delivery_destination_code'))</th>
          <th>@sortablelink('delivery_destination_name', __('view.master.delivery_destinations.index_delivery_destination_name'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($delivery_destinations as $p)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.delivery_destinations.edit', $p->delivery_destination_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.delivery_destinations.index'))
          <td>
            @if ($p->isDeletable())
            <delete-form route-action="{{ route('master.delivery_destinations.delete', $p->delivery_destination_code) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $p->delivery_destination_code }}</td>
          <td class="text-left">{{ $p->delivery_destination_name }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
