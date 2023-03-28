@extends('layout')

@section('title')
{{ __('view.master.customers.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li><a href="{{ route('master.customers.index') }}">{{ __('view.master.customers.index') }}</a></li>
  <li>{{ __('view.master.customers.edit') }}</li>
@endsection

@section('content')
<form id="save-customer-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.customers.update', $customer->customer_code) }}" method="POST">
  <div class="row">
    <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.customers.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
      @canAccess(Auth::user(), route_relatively('master.end_users.index'))
      <button id="search-end-users-via-customer" class="btn btn-default btn-lg" type="button">
        <i class="fa fa-location-arrow"></i> {{ __('view.master.end_users.index') }}
      </button>
      @endcanAccess
    </div>
    <div class="col-md-3 col-sm-3 col-xs-4">
      @canSave(Auth::user(), route_relatively('master.customers.index'))
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
      @endcanSave
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="customer_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.customer_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $customer->customer_code }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="customer_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.customer_name1') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="customer_name" class="form-control ime-active {{ has_error('customer_name') }}" maxlength="50" name="customer_name" type="text" value="{{ old('customer_name', $customer->customer_name) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="customer_name2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.customers.customer_name2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="customer_name2" class="form-control ime-active {{ has_error('customer_name2') }}" maxlength="50" name="customer_name2" type="text" value="{{ old('customer_name2', $customer->customer_name2) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="customer_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.customer_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="customer_abbreviation" class="form-control ime-active {{ has_error('customer_abbreviation') }}" maxlength="20" name="customer_abbreviation" type="text" value="{{ old('customer_abbreviation', $customer->customer_abbreviation) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="customer_name_kana" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.customer_name_kana') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="customer_name_kana" class="form-control ime-active {{ has_error('customer_name_kana') }}" maxlength="30" name="customer_name_kana" type="text" value="{{ old('customer_name_kana', $customer->customer_name_kana) }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="customer_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.customers.customer_name_english') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="customer_name_english" class="form-control ime-inactive {{ has_error('customer_name_english') }}" maxlength="65" name="customer_name_english" type="text" value="{{ old('customer_name_english', $customer->customer_name_english) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="country_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.country_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="country_code"
                 class="form-control text-center ime-inactive {{ has_error('country_code') }}"
                 maxlength="{{ $country_code->getMaxLength() }}"
                 name="country_code"
                 type="text"
                 value="{{ old('country_code', $customer->country_code) }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($country_code->getHelpText()) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="postal_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.postal_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="postal_code"
                 class="form-control ime-inactive {{ has_error('postal_code') }}"
                 maxlength="{{ $postal_code->getMaxLength() }}"
                 name="postal_code"
                 type="text"
                 value="{{ old('postal_code', $customer->postal_code) }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($postal_code->getHelpText()) }}"
                 required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="prefecture_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.prefecture_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="prefecture_code" class="form-control {{ has_error('prefecture_code') }}" name="prefecture_code" data-toggle="tooltip" title="{{ replace_el($prefecture_code->getHelpText()) }}">
            <option value=""></option>
            @foreach ($prefecture_code->all() as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code', $customer->prefecture_code)) }}>{{ $value }}: {{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.address') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address" class="form-control ime-active {{ has_error('address') }}" maxlength="50" name="address" type="text" value="{{ old('address', $customer->address) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address2" class="form-control ime-active {{ has_error('address2') }}" maxlength="50" name="address2" type="text" value="{{ old('address2', $customer->address2) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address3" class="form-control ime-active {{ has_error('address3') }}" maxlength="50" name="address3" type="text" value="{{ old('address3', $customer->address3) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address" class="form-control ime-inactive {{ has_error('abroad_address') }}" maxlength="50" name="abroad_address" type="text" value="{{ old('abroad_address', $customer->abroad_address) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="abroad_address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address2" class="form-control ime-inactive {{ has_error('abroad_address2') }}" maxlength="50" name="abroad_address2" type="text" value="{{ old('abroad_address2', $customer->abroad_address2) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address3" class="form-control ime-inactive {{ has_error('abroad_address3') }}" maxlength="50" name="abroad_address3" type="text" value="{{ old('abroad_address3', $customer->abroad_address3) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="phone_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.phone_number') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="phone_number" class="form-control ime-inactive {{ has_error('phone_number') }}" maxlength="20" name="phone_number" type="text" value="{{ old('phone_number', $customer->phone_number) }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="extension_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.extension_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="extension_number" class="form-control ime-inactive {{ has_error('extension_number') }}" maxlength="15" name="extension_number" type="text" value="{{ old('extension_number', $customer->extension_number) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="fax_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.fax_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="fax_number" class="form-control ime-inactive {{ has_error('fax_number') }}" maxlength="15" name="fax_number" type="text" value="{{ old('fax_number', $customer->fax_number) }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.mail_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="mail_address" class="form-control ime-inactive {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address', $customer->mail_address) }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="closing_date" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.closing_date') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="closing_date" class="form-control {{ has_error('closing_date') }}" name="closing_date" required>
            @foreach ($closing_date_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('closing_date', $customer->closing_date)) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="payment_timing_month" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.payment_timing') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="payment_timing_month" class="form-control {{ has_error('payment_timing_month') }}" name="payment_timing_month" required>
            @foreach ($payment_timing_month_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('payment_timing_month', $customer->payment_timing_month)) }}>{{ $label }}</option>
            @endforeach
          </select>
          <select id="payment_timing_date" class="form-control {{ has_error('payment_timing_date') }}" name="payment_timing_date" required>
            @foreach ($payment_timing_date_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('payment_timing_date', $customer->payment_timing_date)) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="basis_for_recording_sales" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.basis_for_recording_sales') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="basis_for_recording_sales" class="form-control {{ has_error('basis_for_recording_sales') }}" name="basis_for_recording_sales" required>
            @foreach ($basis_for_recording_sales_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('basis_for_recording_sales', $customer->basis_for_recording_sales)) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="rounding_type" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.rounding_type') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="rounding_type" class="form-control {{ has_error('rounding_type') }}" name="rounding_type" required>
            @foreach ($rounding_type_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('rounding_type', $customer->rounding_type)) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="can_display" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.global.can_display') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          @foreach ($can_display_list as $label => $value)
          <label class="radio-inline">
            <input type="radio" name="can_display" id="can_display_{{ $value }}" value="{{ $value }}" {{ is_checked($value, old('can_display', $customer->can_display)) }}>{{ $label }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="order_cooperation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.order_cooperation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          @foreach ($order_cooperation_list as $label => $value)
          <label class="radio-inline">
            <input type="radio" name="order_cooperation" id="order_cooperation_{{ $value }}" value="{{ $value }}" {{ is_checked($value, old('order_cooperation', $customer->order_cooperation)) }}>{{ $label }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.global.remark') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <textarea id="remark" class="form-control ime-active {{ has_error('remark') }}" name="remark" rows="3">{{ old('remark', $customer->remark) }}</textarea>
        </div>
      </div>
    </div>
  </div>
  <input name="updated_at" type="hidden" value="{{ $customer->updated_at->format('Y-m-d H:i:s') }}">
  {{ method_field('PATCH') }}
  {{ csrf_field() }}

  @canSave(Auth::user(), route_relatively('master.customers.index'))
  <input id="can-save-data" type="hidden" value="1">
  @else
  <input id="can-save-data" type="hidden" value="0">
  @endcanSave
</form>

<form id="search-end-users-via-customer-form" action="{{ route('master.end_users.search') }}" method="POST">
  <input name="customer_code" type="hidden" value="{{ $customer->customer_code }}">
  <input name="past_flag" type="hidden" value="0">
  {{ method_field('POST') }}
  {{ csrf_field() }}
</form>
@endsection

@section('scripts')
  @parent

  <script type="text/javascript">
    $('#search-end-users-via-customer').click(function () {
      $('#search-end-users-via-customer-form').submit()
    })
  </script>
@endsection
