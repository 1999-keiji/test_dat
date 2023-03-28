@extends('layout')

@section('title')
{{ __('view.master.transport_companies.index') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.master.transport_companies.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('master.transport_companies.search') }}" method="POST">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered">
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.transport_companies.transport_company_name') }}</th>
          <td class="col-md-3 col-sm-3 col-xs-3">
            <input class="form-control ime-inactive {{ has_error('transport_company_name') }}" maxlength="50" name="transport_company_name" value="{{ old('transport_company_name', $params['transport_company_name'] ?? '') }}">
          </td>
          <th class="col-md-2 col-sm-2 col-xs-2">{{ __('view.master.transport_companies.transport_branch_name') }}</th>
          <td class="col-md-5 col-sm-5 col-xs-5">
            <input class="form-control ime-active {{ has_error('transport_branch_name') }}" maxlength="50" name="transport_branch_name" value="{{ old('transport_branch_name', $params['transport_branch_name'] ?? '') }}">
          </td>
        </tr>
      </table>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-3">
      @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('master.transport_companies.add') }}">
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
    <p><span class="search-result-count">{{ $transport_companies->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}</p>
  </div>
</div>
<div class="row">
  <div class="col-md-9 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
          <th>{{ __('view.global.delete') }}</th>
          @endcanSave
          <th>@sortablelink('transport_company_code', __('view.master.transport_companies.transport_company_code'))</th>
          <th>@sortablelink('transport_company_name', __('view.master.transport_companies.transport_company_name'))</th>
          <th>@sortablelink('transport_branch_name', __('view.master.transport_companies.transport_branch_name'))</th>
          <th>@sortablelink('transport_company_abbreviation', __('view.master.transport_companies.transport_company_abbreviation'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($transport_companies as $t)
        <tr>
          <td>
            <a class="btn btn-sm btn-info" href="{{ route('master.transport_companies.edit', $t->transport_company_code) }}">
              {{ __('view.global.edit') }}
            </a>
          </td>
          @canSave(Auth::user(), route_relatively('master.transport_companies.index'))
          <td>
            <delete-form route-action="{{ route('master.transport_companies.delete', $t->transport_company_code) }}">
            </delete-form>
          </td>
          @endcanSave
          <td class="text-left">{{ $t->transport_company_code }}</td>
          <td class="text-left">{{ $t->transport_company_name }}</td>
          <td class="text-left">{{ $t->transport_branch_name }}</td>
          <td class="text-left">{{ $t->transport_company_abbreviation }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
