@extends('layout')

@section('title')
{{ __('view.master.end_users.add') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.end_users.index') }}">{{ __('view.master.end_users.index') }}</a>
  </li>
  <li>{{ __('view.master.end_users.add') }}</li>
@endsection

@section('content')
<form id="save-end_user-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.end_users.create') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.end_users.index') }}">
        <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
      </a>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button class="btn btn-default btn-lg pull-right save-data" type="button">
        <i class="fa fa-save"></i> {{ __('view.global.save') }}
      </button>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
      	<label for="end_user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
      		{{ __('view.master.end_users.end_user_code') }}
      		<span class="required-mark">*</span>
      	</label>
      	<div class="col-md-7 col-sm-7">
          <input id="end_user_code"
                 class="form-control ime-inactive {{ has_error('end_user_code') }}"
                 maxlength="{{ $end_user_code->getMaxLength() }}"
                 name="end_user_code"
                 type="text"
                 value="{{ old('end_user_code') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($end_user_code->getHelpText()) }}"
                 required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.creating_type') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <span class="shown_label">{{ $creating_type->label() }}</span>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="application_started_on" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.application_started_on') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <datepicker-ja attr-name="application_started_on" date="{{ old('application_started_on') }}"></datepicker-ja>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="customer" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.customers.customer') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="customer_code" class="form-control {{ has_error('customer_code') }}" name="customer_code" required>
            <option value=""></option>
            @foreach ($customers as $c)
            <option value="{{ $c->customer_code }}" {{ is_selected($c->customer_code, old('customer_code', '')) }}>{{ $c->customer_abbreviation }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="end_user_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.end_user_name1') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_name" class="form-control ime-active {{ has_error('end_user_name') }}" maxlength="50" name="end_user_name" type="text" value="{{ old('end_user_name') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="end_user_name2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.end_user_name2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_name2" class="form-control ime-active {{ has_error('end_user_name2') }}" maxlength="50" name="end_user_name2" type="text" value="{{ old('end_user_name2') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="end_user_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.end_user_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_abbreviation" class="form-control ime-active {{ has_error('end_user_abbreviation') }}" maxlength="20" name="end_user_abbreviation" type="text" value="{{ old('end_user_abbreviation') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="end_user_name_kana" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.end_user_name_kana') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_name_kana" class="form-control ime-active {{ has_error('end_user_name_kana') }}" maxlength="30" name="end_user_name_kana" type="text" value="{{ old('end_user_name_kana') }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="end_user_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.end_user_name_english') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_name_english" class="form-control ime-inactive {{ has_error('end_user_name_english') }}" maxlength="65" name="end_user_name_english" type="text" value="{{ old('end_user_name_english') }}">
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
                 class="form-control ime-inactive text-center {{ has_error('country_code') }}"
                 maxlength="{{ $country_code->getMaxLength() }}"
                 name="country_code"
                 type="text"
                 value="{{ old('country_code') }}"
                 data-toggle="tooltip"
                 title="{{ replace_el($country_code->getHelpText()) }}"
                 required>
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
                 value="{{ old('postal_code') }}"
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
            <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code', '')) }}>{{ $value }}: {{ $label }}</option>
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
          <input id="address" class="form-control ime-active {{ has_error('address') }}" maxlength="50" name="address" type="text" value="{{ old('address') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.address2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="address2" class="form-control ime-active {{ has_error('address2') }}" maxlength="50" name="address2" type="text" value="{{ old('address2') }}">
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
          <input id="address3" class="form-control ime-active {{ has_error('address3') }}" maxlength="50" name="address3" type="text" value="{{ old('address3') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address" class="form-control ime-active {{ has_error('abroad_address') }}" maxlength="50" name="abroad_address" type="text" value="{{ old('abroad_address') }}">
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
          <input id="abroad_address2" class="form-control ime-active {{ has_error('abroad_address2') }}" maxlength="50" name="abroad_address2" type="text" value="{{ old('abroad_address2') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="abroad_address3" class="form-control ime-active {{ has_error('abroad_address3') }}" maxlength="50" name="abroad_address3" type="text" value="{{ old('abroad_address3') }}">
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
          <input id="phone_number" class="form-control ime-inactive {{ has_error('phone_number') }}" maxlength="20" name="phone_number" type="text" value="{{ old('phone_number') }}" required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.mail_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="mail_address" class="form-control ime-inactive {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="end_user_staff_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.end_user_staff_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_staff_name" class="form-control ime-active {{ has_error('end_user_staff_name') }}" maxlength="30" name="end_user_staff_name" type="text" value="{{ old('end_user_staff_name') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="currency_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.currency_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="currency_code" class="form-control {{ has_error('currency_code') }}" name="currency_code" required>
            <option value=""></option>
            @foreach ($currencies as $c)
            <option value="{{ $c->currency_code }}" {{ is_selected($c->currency_code, old('currency_code', '')) }}>{{ $c->currency_code }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="delivery_destination" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <search-master target="delivery_destination" code="{{ old('delivery_destination_code') }}" name="{{ old('delivery_destination_name') }}">
          </search-master>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="seller_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.seller_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="seller_code" class="form-control ime-inactive {{ has_error('seller_code') }}" maxlength="8" name="seller_code" type="text" value="{{ old('seller_code') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="seller_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.seller_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="seller_name" class="form-control ime-active {{ has_error('seller_name') }}" maxlength="30" name="seller_name" type="text" value="{{ old('seller_name') }}" required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="pickup_slip_message" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.pickup_slip_message') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="pickup_slip_message" class="form-control ime-active {{ has_error('pickup_slip_message') }}" maxlength="40" name="pickup_slip_message" type="text" value="{{ old('pickup_slip_message') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="statement_of_delivery_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.statement_of_delivery_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="statement_of_delivery_class" class="form-control {{ has_error('statement_of_delivery_class') }}" name="statement_of_delivery_class" required>
            @foreach ($statement_of_delivery_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, '1') }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="statement_of_delivery_price_show_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.statement_of_delivery_price_show_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="statement_of_delivery_price_show_class" class="form-control {{ has_error('statement_of_delivery_price_show_class') }}" name="statement_of_delivery_price_show_class" required>
            @foreach ($statement_of_delivery_price_show_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, '2') }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_shipment_price_show_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.abroad_shipment_price_show_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="abroad_shipment_price_show_class" class="form-control {{ has_error('abroad_shipment_price_show_class') }}" name="abroad_shipment_price_show_class" required>
            <option value=""></option>
            @foreach ($abroad_shipment_price_show_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('abroad_shipment_price_show_class', '')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="export_managing_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.export_managing_class') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="export_managing_class" class="form-control {{ has_error('export_managing_class') }}" name="export_managing_class">
            <option value=""></option>
            @foreach ($export_managing_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('export_managing_class', '')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="export_exchange_rate_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.export_exchange_rate_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="export_exchange_rate_code" class="form-control {{ has_error('export_exchange_rate_code') }}" name="export_exchange_rate_code">
            <option value=""></option>
            @foreach ($export_exchange_rate_code_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('export_exchange_rate_code', '')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="remarks1" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks1') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks1" class="form-control ime-active {{ has_error('remarks1') }}" maxlength="50" name="remarks1" type="text" value="{{ old('remarks1') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="remarks2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks2" class="form-control ime-active {{ has_error('remarks2') }}" maxlength="50" name="remarks2" type="text" value="{{ old('remarks2') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="remarks3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks3" class="form-control ime-active {{ has_error('remarks3') }}" maxlength="50" name="remarks3" type="text" value="{{ old('remarks3') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="remarks4" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks4') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks4" class="form-control ime-active {{ has_error('remarks4') }}" maxlength="50" name="remarks4" type="text" value="{{ old('remarks4') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="remarks5" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks5') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks5" class="form-control ime-active {{ has_error('remarks5') }}" maxlength="50" name="remarks5" type="text" value="{{ old('remarks5') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="remarks6" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.remarks6') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="remarks6" class="form-control ime-active {{ has_error('remarks6') }}" maxlength="50" name="remarks6" type="text" value="{{ old('remarks6') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="loading_port_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.loading_port_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="loading_port_code" class="form-control ime-inactive {{ has_error('loading_port_code') }}" maxlength="4" name="loading_port_code" type="text" value="{{ old('loading_port_code') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="loading_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.loading_port_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="loading_port_name" class="form-control ime-active {{ has_error('loading_port_name') }}" maxlength="30" name="loading_port_name" type="text" value="{{ old('loading_port_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="drop_port_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.drop_port_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="drop_port_code" class="form-control ime-inactive {{ has_error('drop_port_code') }}" maxlength="4" name="drop_port_code" type="text" value="{{ old('drop_port_code') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="drop_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.drop_port_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="drop_port_name" class="form-control ime-active {{ has_error('drop_port_name') }}" maxlength="30" name="drop_port_name" type="text" value="{{ old('drop_port_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="exchange_rate_port_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.exchange_rate_port_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="exchange_rate_port_code" class="form-control ime-inactive {{ has_error('exchange_rate_port_code') }}" maxlength="4" name="exchange_rate_port_code" type="text" value="{{ old('exchange_rate_port_code') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="exchange_rate_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.exchange_rate_port_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="exchange_rate_port_name" class="form-control ime-active {{ has_error('exchange_rate_port_name') }}" maxlength="30" name="exchange_rate_port_name" type="text" value="{{ old('exchange_rate_port_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="lot_managing_target_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.lot_managing_target_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="lot_managing_target_flag" name="lot_managing_target_flag" type="checkbox" value="1" {{ is_checked(1, old('lot_managing_target_flag', 0)) }}>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="end_user_remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.end_user_remark') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_remark" class="form-control ime-active {{ has_error('end_user_remark') }}" maxlength="50" name="end_user_remark" type="text" value="{{ old('end_user_remark') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="end_user_request_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.end_user_request_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="end_user_request_number" class="form-control ime-inactive {{ has_error('end_user_request_number') }}" maxlength="5" name="end_user_request_number" type="text" value="{{ old('end_user_request_number') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="statement_of_delivery_remark_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.statement_of_delivery_remark_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="statement_of_delivery_remark_class" class="form-control {{ has_error('statement_of_delivery_remark_class') }}" name="statement_of_delivery_remark_class" required>
            @foreach ($statement_of_delivery_remark_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, '1') }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="statement_of_delivery_buyer_remark_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.statement_of_delivery_buyer_remark_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="statement_of_delivery_buyer_remark_class" class="form-control {{ has_error('statement_of_delivery_buyer_remark_class') }}" name="statement_of_delivery_buyer_remark_class" required>
            <option value=""></option>
            @foreach ($statement_of_delivery_buyer_remark_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('statement_of_delivery_buyer_remark_class', '')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="export_target_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.export_target_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="export_target_flag" name="export_target_flag" type="checkbox" value="1" {{ is_checked(1, old('export_target_flag', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="group_company_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.group_company_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="group_company_flag" name="group_company_flag" type="checkbox" value="1" {{ is_checked(1, old('group_company_flag', 0)) }}>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="company_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_code" class="form-control ime-inactive {{ has_error('company_code') }}" maxlength="50" name="company_code" type="text" value="{{ old('company_code') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="company_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_name" class="form-control ime-active {{ has_error('company_name') }}" maxlength="50" name="company_name" type="text" value="{{ old('company_name') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="company_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_abbreviation') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_abbreviation" class="form-control ime-active {{ has_error('company_abbreviation') }}" maxlength="20" name="company_abbreviation" type="text" value="{{ old('company_abbreviation') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="company_name_kana" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_name_kana') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_name_kana" class="form-control ime-active {{ has_error('company_name_kana') }}" maxlength="30" name="company_name_kana" type="text" value="{{ old('company_name_kana') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="company_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_name_english') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_name_english" class="form-control ime-inactive {{ has_error('company_name_english') }}" maxlength="50" name="company_name_english" type="text" value="{{ old('company_name_english') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="company_group_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_group_code') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_group_code" class="form-control ime-inactive {{ has_error('company_group_code') }}" maxlength="8" name="company_group_code" type="text" value="{{ old('company_group_code') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="company_group_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_group_name') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_group_name" class="form-control ime-active {{ has_error('company_group_name') }}" maxlength="40" name="company_group_name" type="text" value="{{ old('company_group_name') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="company_group_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.end_users.company_group_name_english') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="company_group_name_english" class="form-control ime-inactive {{ has_error('company_group_name_english') }}" maxlength="8" name="company_group_name_english" type="text" value="{{ old('company_group_name_english') }}">
        </div>
      </div>
    </div>
  </div>

  @include('_include.add_reserved_form')
  {{ csrf_field() }}
  {{ method_field('POST') }}
</form>
@endsection
