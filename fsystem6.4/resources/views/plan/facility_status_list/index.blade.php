@extends('layout')

@section('title')
{{ __('view.plan.facility_status_list.index') }}
@endsection

@section('menu-section')
{{ __('view.index.production-plan') }}
@endsection

@section('breadcrumbs')
  <li>{{ __('view.plan.facility_status_list.index') }}</li>
@endsection

@section('content')
<div class="row">
  <form class="form-horizontal basic-form" action="{{ route('plan.facility_status_list.export') }}" method="GET">
    <div class="col-md-6 col-sm-7 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered table-layout-fixed">
        <tbody>
          <tr>
            <th>
              {{ __('view.master.factories.factory') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <select class="form-control" name="factory_code" required>
                <option value=""></option>
                @foreach ($factories as $f)
                  <option value="{{ $f->factory_code }}" {{ is_selected($f->factory_code, old('factory_code')) }}>{{ $f->factory_abbreviation }}</option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
            <th>
              {{ __('view.factory_production_work.work_instruction.working_date') }}
              <span class="required-mark">*</span>
            </th>
            <td>
              <datepicker-ja attr-name="working_date" date="{{ old('working_date')}}"></datepicker-ja>
              から４週間
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2 col-xs-12">
      <button class="btn btn-lg btn-default remove-alert" type="submit">
        <i class="fa fa-download"></i> {{ __('view.global.excel_download') }}
      </button>
    </div>
  </form>
</div>
@endsection
