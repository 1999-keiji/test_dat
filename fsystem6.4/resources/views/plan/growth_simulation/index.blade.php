@extends('layout')

@section('title')
{{ __('view.plan.growth_simulation.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.growth_simulation.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form action="{{ route('plan.growth_simulation.search') }}" method="POST">
    <div class="col-md-7 col-sm-6 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <search-growth-simulations-form
        :factories="{{ $factories }}"
        :search-params="{{ json_encode($params ?: new \stdClass()) }}"
        :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
        :fixed-flag="{{ json_encode(false) }}">
      </search-growth-simulations-form>
    </div>
    <div class="col-md-3 col-md-pull-1 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-2">
      @canSave(Auth::user(), route_relatively('plan.growth_simulation.index'))
      <a class="btn btn-lg btn-default pull-right" href="{{ route('plan.growth_simulation.add') }}">
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
      <span class="search-result-count">{{ $growth_simulations->total() }}</span>{{ __('view.global.suffix_serach_result_count') }}
    </p>
  </div>
  <div class="col-md-6 col-sm-6 col-md-offset-2 col-sm-offset-2">
    {{ $growth_simulations->appends(request(['sort', 'order']))->links() }}
  </div>
</div>

<div class="row">
  <div class="col-md-10 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered table-more-condensed">
      <thead>
        <tr>
          <th>{{ __('view.global.edit') }}</th>
          <th>{{ __('view.global.delete') }}</th>
          <th>{{ __('view.global.fix') }}</th>
          <th>{{ __('view.global.harvest') }}</th>
          <th>@sortablelink('factory_code', __('view.master.factories.factory'))</th>
          <th>@sortablelink('factory_species_code', __('view.master.factory_species.factory_species'))</th>
          <th>@sortablelink('simulation_name', __('view.plan.growth_simulation.simulation_name'))</th>
          <th>@sortablelink('work_by', __('view.plan.growth_simulation.working'))</th>
          <th>@sortablelink('created_by', __('view.plan.growth_simulation.created_by'))</th>
          <th>@sortablelink('simulation_preparation_comp_at', __('view.plan.growth_simulation.created_at'))</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($growth_simulations as $gs)
        <tr>
          <td>
          @if (! $gs->hasPrepared())
            <a class="btn btn-sm btn-default active disabled">{{ __('view.global.adding') }}</a>
          @else
            @if ($gs->isUnlockedSimulation())
              <form action="{{ route('plan.growth_simulation.lock', $gs->getJoinedPrimaryKeys()) }}" method="POST">
                <button class="btn btn-sm btn-info" type="submit">{{ __('view.global.edit') }}</button>
                {{ csrf_field() }}
                {{ method_field('PATCH') }}
              </form>
            @elseif ($gs->isSimulatingByLoginedUser())
              <a class="btn btn-sm btn-primary" href="{{ route('plan.growth_simulation.edit', $gs->getJoinedPrimaryKeys()) }}">{{ __('view.global.reference') }}</a>
            @else
              <a class="btn btn-sm btn-default active disabled">{{ __('view.global.lock') }}</a>
            @endif
          @endif
          </td>
          <td>
          @if (! $gs->hasPrepared())
            <a class="btn btn-sm btn-default active disabled">{{ __('view.global.adding') }}</a>
          @elseif ($gs->canSimulate())
            <delete-form
              route-action="{{ route('plan.growth_simulation.delete', $gs->getJoinedPrimaryKeys()) }}">
            </delete-form>
          @else
            <a class="btn btn-sm btn-default active disabled">{{ __('view.global.lock') }}</a>
          @endif
          </td>
          <td>
          @if (! $gs->hasPrepared())
            <a class="btn btn-sm btn-default active disabled">{{ __('view.global.adding') }}</a>
          @elseif ($gs->canSimulate())
            <fix-simulation
              first-harvesting-date="{{ $gs->getFirstHarvestingDate() }}"
              href-to-check-bed-number="{{ route('plan.growth_simulation.check_bed_number', $gs->getJoinedPrimaryKeys()) }}"
              action-of-fix-simulation="{{ route('plan.growth_simulation.fix', $gs->getJoinedPrimaryKeys()) }}"
              href-to-index-of-fixed-simulations="{{ route('plan.growth_simulation_fixed.index') }}">
            </fix-simulation>
          @else
            <a class="btn btn-sm btn-default active disabled">{{ __('view.global.lock') }}</a>
          @endif
          </td>
          <td>
            <growth-simulation-details
              :growth-simulation="{{ $gs }}"
              :growth-simulation-item-lists="{{ $gs->growth_simulation_items->groupByInputGroup() }}"
              :growing-stage-list="{{ json_encode($growing_stage_list) }}">
            </growth-simulation-details>
          </td>
          <td class="text-left">{{ $gs->factory_abbreviation }}</td>
          <td class="text-left">{{ $gs->factory_species_name }}</td>
          <td class="text-left">{{ $gs->simulation_name }}</td>
          @if (! is_null($gs->simulation_preparation_start_at) &&! $gs->hasPrepared())
            <td>準備中（{{ $gs->simulation_preparation_start_at->format('Y/m/d H:i:s') }}）</td>
          @else
            @if (! $gs->isUnlockedSimulation())
              @if ($gs->isSimulatingByLoginedUser())
                <td>
                  <form action="{{ route('plan.growth_simulation.unlock', $gs->getJoinedPrimaryKeys()) }}" method="POST">
                    <button class="btn btn-sm btn-unlock" type="submit">{{ __('view.global.unlock') }}</button>
                    {{ csrf_field() }}
                    {{ method_field('PATCH') }}
                  </form>
                </td>
              @else
                <td class="text-left">{{ $gs->work_name }}（{{ $gs->work_at->format('Y/m/d H:i') }}）</td>
              @endif
            @else
              <td></td>
            @endif
          @endif
          <td class="text-left">{{ $gs->created_name }}</td>
          <td>
            @if ($gs->hasPrepared())
            {{ $gs->simulation_preparation_comp_at->format('Y/m/d H:i:s') }}
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif
@endsection
