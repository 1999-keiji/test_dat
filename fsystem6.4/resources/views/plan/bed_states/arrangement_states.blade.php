@extends('layout')

@section('title')
{{ __('view.plan.planned_arrangement_status_work.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.bed_states.index') }}">{{ __('view.plan.bed_states.index') }}</a>
  </li>
  <li>{{ __('view.plan.planned_arrangement_status_work.index') }}</li>
@endsection

@section('content')
<bed-states
  :factory="{{ $bed_state->factory_species->factory }}"
  :factory-species="{{ $bed_state->factory_species }}"
  :working-date="{{ $working_date->toJson($bed_state, $bed_state->factory_species->factory) }}"
  :factory-growing-stages="{{ $factory_growing_stages->toJson() }}"
  :factory-layout="{{ json_encode($factory_layout) }}"
  href-to-previous="{{ route('plan.bed_states.index') }}"
  href-to-export-data="{{ route('plan.bed_states.arrangement_states.export', [$bed_state->getJoinedPrimaryKeys(), $working_date->format('Y-m-d')]) }}"
  href-to-cultivation-states="{{ route('plan.bed_states.cultivation_states.index', $bed_state->getJoinedPrimaryKeys()) }}"
  href-to-cultivation-states-sum="{{ route('plan.bed_states.cultivation_states.sum', $bed_state->getJoinedPrimaryKeys()) }}">
</bed-states>
@endsection
