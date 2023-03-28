@extends('layout')

@section('title')
{{ __('view.plan.growth_sale_management_summary.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_sale_management_summary.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form  class="form-horizontal basic-form" action="{{ route('plan.growth_sale_management_summary.search') }}" method="POST">
    <div class="col-md-10 col-sm-12 col-xs-12 col-md-offset-1">
      <search-growth-management-summary-form
        :factories="{{ $factories }}"
        :species-list="{{ $species_list }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :belongs-to-factory="{{ json_encode(Auth::user()->affiliation->belongsToFactory()) }}"
        default-harvesting-date="{{ $default_harvesting_date }}">
      </search-growth-management-summary-form>
    </div>
    <div class="col-md-10 col-sm-12 col-xs-12 col-md-offset-1">
      <button class="btn btn-lg btn-default pull-right" type="submit">
        <i class="fa fa-search"></i> {{ __('view.global.search') }}
      </button>
    </div>
    {{ csrf_field() }}
    {{ method_field('POST') }}
  </form>
</div>
@endsection
