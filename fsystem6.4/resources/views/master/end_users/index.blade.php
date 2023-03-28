@extends('layout')

@section('title')
{{ __('view.master.end_users.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.end_users.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.end_users.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th>{{ __('view.master.customers.customer_code') }}</th>
          <td>
            <input class="form-control ime-inactive {{ has_error('customer_code') }}" maxlength="{{ $customer_code->getMaxLength() }}" name="customer_code" value="{{ old('customer_code', $params['customer_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.customers.customer_name') }}</th>
          <td>
            <input class="form-control ime-active {{ has_error('customer_name') }}" maxlength="50" name="customer_name" value="{{ old('customer_name', $params['customer_name'] ?? '') }}">
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.end_users.end_user_code') }}</th>
          <td>
            <input class="form-control ime-inactive {{ has_error('end_user_code') }}" maxlength="{{ $end_user_code->getMaxLength() }}" name="end_user_code" value="{{ old('end_user_code', $params['end_user_code'] ?? '') }}">
          </td>
          <th>{{ __('view.master.end_users.end_user_name') }}</th>
          <td>
            <input class="form-control ime-active {{ has_error('end_user_name') }}" maxlength="50" name="end_user_name" value="{{ old('end_user_name', $params['end_user_name'] ?? '') }}">
          </td>
        </tr>
        <tr>
          <th>{{ __('view.master.end_users.past_flag') }}</th>
          <td>
            <label class="radio-inline">
              <input type="radio" name="past_flag" value="1" {{ is_checked(1, $params['past_flag'] ?? 0) }}>ON
            </label>
            <label class="radio-inline">
              <input type="radio" name="past_flag" value="0" {{ is_checked(0, $params['past_flag'] ?? 0) }}>OFF
            </label>
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-1 col-sm-offset-1">
      @canSave(Auth::user(), route_relatively('master.end_users.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.end_users.add') }}">
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
  <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
    <p>
      <span class="search-result-count">{{ $end_users->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $end_users->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-11 col-sm-11 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.end_users.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('customer_code', __('view.master.customers.customer'))</th>
          <th>@sortablelink('end_user_code', __('view.master.end_users.end_user_code'))</th>
          <th>@sortablelink('application_started_on', __('view.master.end_users.application_started_on'))</th>
          <th>@sortablelink('end_user_name', __('view.master.end_users.end_user_name'))</th>
          <th>@sortablelink('address', __('view.master.global.address'))</th>
          <th>@sortablelink('phone_number', __('view.master.global.phone_number'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($end_users as $eu)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.end_users.edit', $eu->getJoinedPrimaryKeys()) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.end_users.index'))
          <td>
            @if ($eu->isDeletable())
            <delete-form route-action="{{ route('master.end_users.delete', $eu->getJoinedPrimaryKeys()) }}">
            </delete-form>
            @endif
          </td>
          @endcanSave
          <td class="text-left">{{ $eu->customer_abbreviation }}</td>
          <td class="text-left">{{ $eu->end_user_code }}</td>
          <td>{{ $eu->application_started_on }}</td>
          <td class="text-left">{{ $eu->end_user_name }}</td>
          <td class="text-left">{{ $eu->address }}</td>
          <td class="text-left">{{ $eu->phone_number }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
