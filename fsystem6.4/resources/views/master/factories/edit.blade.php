@extends('layout')

@section('title')
{{ __('view.master.factories.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li><a href="{{ route('master.factories.index') }}">{{ __('view.master.factories.index') }}</a></li>
  <li>{{ __('view.master.factories.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factories.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factory_species.index', $factory->factory_code) }}">
      <i class="fa fa-location-arrow"></i> {{ __('view.master.factories.factory_species') }}
    </a>
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factory_products.index', $factory->factory_code) }}">
      <i class="fa fa-location-arrow"></i> {{ __('view.master.factories.factory_products') }}
    </a>
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.factory_rest.index', $factory->factory_code) }}">
      <i class="fa fa-location-arrow"></i> {{ __('view.master.factories.factory_rest') }}
    </a>
  </div>
</div>
<ul class="nav nav-tabs">
  @yield('nav-tabs')
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="factories-tab">
    @yield('edit_content')
  </div>
</div>
@endsection
