@extends('layout')

@section('title')
{{ __('view.plan.planned_arrangement_status_work.detail') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('plan.bed_states.index', $bed_state->getJoinedPrimaryKeys()) }}">
      {{ __('view.plan.bed_states.index') }}
    </a>
  </li>
  <li>
    <a href="{{ route('plan.bed_states.arrangement_states.index', [$bed_state->getJoinedPrimaryKeys(), $working_date->format('Y-m-d')]) }}">
      {{ __('view.plan.planned_arrangement_status_work.index') }}
    </a>
  </li>
  <li>{{ __('view.plan.planned_arrangement_status_work.detail') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 col-sm-8 col-xs-5">
    <a class="btn btn-default btn-lg back-button can-transition" href="{{ route('plan.bed_states.arrangement_states.index', [$bed_state->getJoinedPrimaryKeys(), $working_date->format('Y-m-d')]) }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-7">
    <a class="btn btn-default btn-lg pull-right" href="{{ route('plan.bed_states.arrangement_states.detail.export', [$bed_state->getJoinedPrimaryKeys(), $working_date->format('Y-m-d'), $factory_layout['floor']]) }}">
      <i class="fa fa-edit"></i> {{ __('view.global.report') }}
    </a>
  </div>
</div>

<div class="row">
  <div class="col-md-11 col-sm-11 col-md-offset-1 col-sm-offset-1">
    <div class="row simulation-dates">
      <div class="col-md-4 col-sm-4 text-right change-simulation-date">
        @php ($options = $working_date->options($bed_state, $factory, 'Y-m-d'))
        <a class="btn btn-default
          @if (! $options['prev_day'])
          disabled
          @endif"
          href="{{ $options['prev_day'] ? route('plan.bed_states.arrangement_states.detail', [$bed_state->getJoinedPrimaryKeys(), $options['prev_day'], $factory_layout['floor']]) : '#' }}">
          <i class="fa fa-angle-left" aria-hidden="true"></i>
          {{ __('view.global.prev_day') }}
        </a>
      </div>
      <div class="col-md-4 col-sm-4 text-center">
        <h3>{{ $working_date->formatToJa() }}</h3>
      </div>
      <div class="col-md-4 col-sm-4 change-simulation-date">
        <a class="btn btn-default
          @if (! $options['next_day'])
          disabled
          @endif"
          href="{{ $options['next_day'] ? route('plan.bed_states.arrangement_states.detail', [$bed_state->getJoinedPrimaryKeys(), $options['next_day'], $factory_layout['floor']]) : '#' }}">
          {{ __('view.global.next_day') }}
          <i class="fa fa-angle-right" aria-hidden="true"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 col-sm-12">
      <table class="table table-bordered table-more-condensed panel-allocation">
        <tbody>
          <tr>
            <td class="border-none" colspan="3"></td>
            @foreach ($factory_layout['circulations'] as $circulation)
              <th colspan="{{ $circulation['count'] + ($circulation['count'] - 1) }}">
                {{ __('view.master.factory_columns.circulation') }}{{ $circulation['circulation'] }}
              </th>
              @if (! $loop->last)
              <td class="border-none"></td>
              @endif
            @endforeach
          </tr>
          <tr>
            <td class="border-none" colspan="3"></td>
            @foreach ($factory_layout['columns'] as $column)
              <th>{{ $column['column_name'] }}</th>
              @if (! $loop->last)
              <td class="border-none"></td>
              @endif
            @endforeach
          </tr>
          <tr>
            <td class="border-none" colspan="{{ 3 + count($factory_layout['columns']) + (count($factory_layout['columns']) - 1) }}"></td>
          </tr>
          @foreach ($factory_layout['rows'] as $row)
            <tr>
              @if ($loop->first)
              <th class="floor-row" rowspan="{{ count($factory_layout['rows']) + (count($factory_layout['columns']) - 1) }}">
                {{ $factory_layout['floor'] }}<br>{{ __('view.master.factory_beds.floor') }}
              </th>
              @endif
              <th class="floor-row">
                {{ __('view.master.factory_beds.panel_back') }}<br>
                {{ $row['row'] }}{{ __('view.master.factory_beds.row') }}<br>
                {{ __('view.master.factory_beds.panel_front') }}
              </th>
              <td class="border-none"></td>
              @foreach ($row['beds'] as $bed)
                <td class="factory-bed">
                  <table>
                    <tbody>
                      @foreach ($bed['panels'] as $panels)
                      <tr>
                        @foreach ($panels as $panel)
                        <td
                          @if ($panel['other_species'])
                          class="other-species"
                          @else
                          style="background-color: #{{ $panel['label_color'] }};"
                          @endif>&nbsp;</td>
                        @endforeach
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </td>
                @if (! $loop->last)
                <td class="border-none"></td>
                @endif
              @endforeach
            </tr>
            @if (! $loop->last)
            <tr>
              <td class="border-none" colspan="{{ 3 + count($factory_layout['columns']) + (count($factory_layout['columns']) - 1) }}"></td>
            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
