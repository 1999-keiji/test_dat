@extends('layout')

@section('title')
{{ __('view.plan.planned_arrangement_status_work.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}">
      {{ __('view.plan.growth_simulation.add') }}
    </a>
  </li>
  <li>{{ __('view.plan.planned_arrangement_status_work.index') }}</li>
@endsection

@section('content')
<allocate-panels
  :factory="{{ $growth_simulation->factory_species->factory }}"
  :factory-species="{{ $growth_simulation->factory_species }}"
  :growth-simulation="{{ $growth_simulation }}"
  :display-kubun-list="{{ $display_kubun_list }}"
  :simulation-date="{{ $simulation_date->toJson($growth_simulation, $growth_simulation->factory_species->factory) }}"
  :number-of-beds="{{ json_encode($number_of_beds) }}"
  :bed-status-options="{{ json_encode($bed_status_options) }}"
  :factory-growing-stages="{{ $factory_growing_stages->toJson() }}"
  :factory-layout="{{ json_encode($factory_layout) }}"
  href-to-previous="{{ route('plan.growth_simulation.edit', $growth_simulation->getJoinedPrimaryKeys()) }}"
  href-to-export-data="{{ route('plan.planned_arrangement_status_work.export', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->format('Y-m-d')]) }}"
  href-to-floor-cultivation-stock="{{ route('plan.planned_cultivation_status_work.index', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->startOfWeek()->format('Y-m-d')]) }}"
  href-to-floor-cultivation-stock-sum="{{ route('plan.planned_cultivation_status_work.sum', [$growth_simulation->getJoinedPrimaryKeys(), $simulation_date->startOfWeek()->format('Y-m-d')]) }}">
</allocate-panels>
@endsection
