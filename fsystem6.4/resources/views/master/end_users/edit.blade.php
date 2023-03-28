@extends('layout')

@section('title')
{{ __('view.master.end_users.edit') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.end_users.index') }}">{{ __('view.master.end_users.index') }}</a>
  </li>
  <li>{{ __('view.master.end_users.edit') }}</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <a class="btn btn-default btn-lg back-button" href="{{ route('master.end_users.index') }}">
      <i class="fa fa-arrow-left"></i> {{ __('view.global.back') }}
    </a>
  </div>
</div>

<ul class="nav nav-tabs">
  <li class="active">
    <a href="#">{{ __('view.master.global.base_data') }}</a>
  </li>
  <li>
    <a href="{{ route('master.end_users.factories', $end_user->getJoinedPrimaryKeys()) }}">{{ __('view.master.factories.factory') }}</a>
  </li>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="end_user_tab">
    <form id="save-end_user-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.end_users.update', $end_user->getJoinedPrimaryKeys()) }}" method="POST">
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          @canSave(Auth::user(), route_relatively('master.end_users.index'))
          <button class="btn btn-default btn-lg pull-right save-data" type="button">
            <i class="fa fa-save"></i> {{ __('view.global.save') }}
          </button>
          @endcanSave
        </div>
      </div>

      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
          	<label for="end_user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          	  {{ __('view.master.end_users.end_user_code') }}
          	</label>
          	<div class="col-md-7 col-sm-7">
          	  <span class="shown_label">{{ $end_user->end_user_code }}</span>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.creating_type') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <span class="shown_label">{{ $end_user->creating_type->label() }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="application_started_on" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.application_started_on') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <span class="shown_label">{{ $end_user->application_started_on }}</span>
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
              <select id="customer_code" class="form-control {{ has_error('customer_code') }}" name="customer_code" required {{ $end_user->addDisabledProp('customer_code') }}>
                <option value=""></option>
                @foreach ($customers as $c)
                <option value="{{ $c->customer_code }}" {{ is_selected($c->customer_code, old('customer_code', $end_user->customer_code)) }}>{{ $c->customer_abbreviation }}</option>
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
              <input id="end_user_name" class="form-control ime-active {{ has_error('end_user_name') }}" maxlength="50" name="end_user_name" type="text" value="{{ old('end_user_name', $end_user->end_user_name) }}" required {{ $end_user->addDisabledProp('end_user_name') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="end_user_name2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.end_user_name2') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="end_user_name2" class="form-control ime-active {{ has_error('end_user_name2') }}" maxlength="50" name="end_user_name2" type="text" value="{{ old('end_user_name2', $end_user->end_user_name2) }}" {{ $end_user->addDisabledProp('end_user_name2') }}>
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
              <input id="end_user_abbreviation" class="form-control ime-active {{ has_error('end_user_abbreviation') }}" maxlength="20" name="end_user_abbreviation" type="text" value="{{ old('end_user_abbreviation', $end_user->end_user_abbreviation) }}" required {{ $end_user->addDisabledProp('end_user_abbreviation') }}>
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
              <input id="end_user_name_kana" class="form-control ime-active {{ has_error('end_user_name_kana') }}" maxlength="30" name="end_user_name_kana" type="text" value="{{ old('end_user_name_kana', $end_user->end_user_name_kana) }}" required {{ $end_user->addDisabledProp('end_user_name_kana') }}>
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
              <input id="end_user_name_english" class="form-control ime-inactive {{ has_error('end_user_name_english') }}" maxlength="65" name="end_user_name_english" type="text" value="{{ old('end_user_name_english', $end_user->end_user_name_english) }}" {{ $end_user->addDisabledProp('end_user_name_english') }}>
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
                     value="{{ old('country_code', $end_user->country_code) }}"
                     data-toggle="tooltip"
                     title="{{ $country_code->getHelpText() }}"
                     required
                     {{ $end_user->addDisabledProp('country_code') }}>
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
                     value="{{ old('postal_code', $end_user->postal_code) }}"
                     data-toggle="tooltip"
                     title="{{ $postal_code->getHelpText() }}"
                     required
                     {{ $end_user->addDisabledProp('postal_code') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="prefecture_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.prefecture_code') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <select id="prefecture_code" class="form-control ime-inactive {{ has_error('prefecture_code') }}" name="prefecture_code" data-toggle="tooltip" title="{{ $prefecture_code->getHelpText() }}" {{ $end_user->addDisabledProp('prefecture_code') }}>
                <option value=""></option>
                @foreach ($prefecture_code->all() as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code', $end_user->prefecture_code)) }}>{{ $value }}: {{ $label }}</option>
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
              <input id="address" class="form-control ime-active {{ has_error('address') }}" maxlength="50" name="address" type="text" value="{{ old('address', $end_user->address) }}" required {{ $end_user->addDisabledProp('address') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="address2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.address2') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="address2" class="form-control ime-active {{ has_error('address2') }}" maxlength="50" name="address2" type="text" value="{{ old('address2', $end_user->address2) }}" {{ $end_user->addDisabledProp('address2') }}>
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
              <input id="address3" class="form-control ime-active {{ has_error('address3') }}" maxlength="50" name="address3" type="text" value="{{ old('address3', $end_user->address3) }}" {{ $end_user->addDisabledProp('address3') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="abroad_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.abroad_address') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="abroad_address" class="form-control ime-active {{ has_error('abroad_address') }}" maxlength="50" name="abroad_address" type="text" value="{{ old('abroad_address', $end_user->abroad_address) }}" {{ $end_user->addDisabledProp('abroad_address') }}>
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
              <input id="abroad_address2" class="form-control ime-active {{ has_error('abroad_address2') }}" maxlength="50" name="abroad_address2" type="text" value="{{ old('abroad_address2', $end_user->abroad_address2) }}" {{ $end_user->addDisabledProp('abroad_address2') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.abroad_address3') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="abroad_address3" class="form-control ime-active {{ has_error('abroad_address3') }}" maxlength="50" name="abroad_address3" type="text" value="{{ old('abroad_address3', $end_user->abroad_address3) }}" {{ $end_user->addDisabledProp('abroad_address3') }}>
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
              <input id="phone_number" class="form-control ime-inactive {{ has_error('phone_number') }}" maxlength="20" name="phone_number" type="text" value="{{ old('phone_number', $end_user->phone_number) }}" required {{ $end_user->addDisabledProp('phone_number') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.global.mail_address') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="mail_address" class="form-control ime-inactive {{ has_error('mail_address') }}" maxlength="250" name="mail_address" type="email" value="{{ old('mail_address', $end_user->mail_address) }}" {{ $end_user->addDisabledProp('mail_address') }}>
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
              <input id="end_user_staff_name" class="form-control ime-active {{ has_error('end_user_staff_name') }}" maxlength="30" name="end_user_staff_name" type="text" value="{{ old('end_user_staff_name', $end_user->end_user_staff_name) }}" {{ $end_user->addDisabledProp('end_user_staff_name') }}>
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
              <select id="currency_code" class="form-control {{ has_error('currency_code') }}" name="currency_code" required {{ $end_user->addDisabledProp('currency_code') }}>
                @foreach ($currencies as $c)
                <option value="{{ $c->currency_code }}" {{ is_selected($c->currency_code, old('currency_code', $end_user->currency_code)) }}>{{ $c->currency_code }}</option>
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
            <div class="col-md-7 col-sm-7" id="delivery_destination">
              <search-master
                target="delivery_destination"
                code="{{ $end_user->delivery_destination_code }}"
                name="{{ $end_user->delivery_destination->delivery_destination_abbreviation }}"
                :disabled="{{ json_encode($end_user->addDisabledProp('delivery_destination_code')) }}">
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
              <input id="seller_code" class="form-control ime-inactive {{ has_error('seller_code') }}" maxlength="8" name="seller_code" type="text" value="{{ old('seller_code', $end_user->seller_code) }}" {{ $end_user->addDisabledProp('seller_code') }}>
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
              <input id="seller_name" class="form-control ime-active {{ has_error('seller_name') }}" maxlength="30" name="seller_name" type="text" value="{{ old('seller_name', $end_user->seller_name) }}" required {{ $end_user->addDisabledProp('seller_name') }}>
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
              <input id="pickup_slip_message" class="form-control ime-active {{ has_error('pickup_slip_message') }}" maxlength="40" name="pickup_slip_message" type="text" value="{{ old('pickup_slip_message', $end_user->pickup_slip_message) }}" {{ $end_user->addDisabledProp('pickup_slip_message') }}>
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
              <select id="statement_of_delivery_class" class="form-control {{ has_error('statement_of_delivery_class') }}" name="statement_of_delivery_class" required {{ $end_user->addDisabledProp('statement_of_delivery_class') }}>
                @foreach ($statement_of_delivery_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $end_user->statement_of_delivery_class) }}>{{ $label }}</option>
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
              <select id="statement_of_delivery_price_show_class" class="form-control {{ has_error('statement_of_delivery_price_show_class') }}" name="statement_of_delivery_price_show_class" required {{ $end_user->addDisabledProp('statement_of_delivery_price_show_class') }}>
                @foreach ($statement_of_delivery_price_show_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $end_user->statement_of_delivery_price_show_class) }}>{{ $label }}</option>
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
              <select id="abroad_shipment_price_show_class" class="form-control {{ has_error('abroad_shipment_price_show_class') }}" name="abroad_shipment_price_show_class" required {{ $end_user->addDisabledProp('abroad_shipment_price_show_class') }}>
                <option value=""></option>
                @foreach ($abroad_shipment_price_show_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, old('abroad_shipment_price_show_class', $end_user->abroad_shipment_price_show_class)) }}>{{ $label }}</option>
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
              <select id="export_managing_class" class="form-control {{ has_error('export_managing_class') }}" name="export_managing_class" {{ $end_user->addDisabledProp('export_managing_class') }}>
                <option value=""></option>
                @foreach ($export_managing_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $end_user->export_managing_class) }}>{{ $label }}</option>
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
              <select id="export_exchange_rate_code" class="form-control {{ has_error('export_exchange_rate_code') }}" name="export_exchange_rate_code" {{ $end_user->addDisabledProp('export_exchange_rate_code') }}>
                <option value=""></option>
                @foreach ($export_exchange_rate_code_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $end_user->export_exchange_rate_code) }}>{{ $label }}</option>
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
              <input id="remarks1" class="form-control ime-active {{ has_error('remarks1') }}" maxlength="50" name="remarks1" type="text" value="{{ old('remarks1', $end_user->remarks1) }}" {{ $end_user->addDisabledProp('remarks1') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="remarks2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.remarks2') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="remarks2" class="form-control ime-active {{ has_error('remarks2') }}" maxlength="50" name="remarks2" type="text" value="{{ old('remarks2', $end_user->remarks2) }}" {{ $end_user->addDisabledProp('remarks2') }}>
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
              <input id="remarks3" class="form-control ime-active {{ has_error('remarks3') }}" maxlength="50" name="remarks3" type="text" value="{{ old('remarks3', $end_user->remarks3) }}" {{ $end_user->addDisabledProp('remarks3') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="remarks4" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.remarks4') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="remarks4" class="form-control ime-active {{ has_error('remarks4') }}" maxlength="50" name="remarks4" type="text" value="{{ old('remarks4', $end_user->remarks4) }}" {{ $end_user->addDisabledProp('remarks4') }}>
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
              <input id="remarks5" class="form-control ime-active {{ has_error('remarks5') }}" maxlength="50" name="remarks5" type="text" value="{{ old('remarks5', $end_user->remarks5) }}" {{ $end_user->addDisabledProp('remarks5') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="remarks6" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.remarks6') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="remarks6" class="form-control ime-active {{ has_error('remarks6') }}" maxlength="50" name="remarks6" type="text" value="{{ old('remarks6', $end_user->remarks6) }}" {{ $end_user->addDisabledProp('remarks6') }}>
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
              <input id="loading_port_code" class="form-control ime-inactive {{ has_error('loading_port_code') }}" maxlength="4" name="loading_port_code" type="text" value="{{ old('loading_port_code', $end_user->loading_port_code) }}" {{ $end_user->addDisabledProp('loading_port_code') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="loading_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.loading_port_name') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="loading_port_name" class="form-control ime-active {{ has_error('loading_port_name') }}" maxlength="30" name="loading_port_name" type="text" value="{{ old('loading_port_name', $end_user->loading_port_name) }}" {{ $end_user->addDisabledProp('loading_port_name') }}>
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
              <input id="drop_port_code" class="form-control ime-inactive {{ has_error('drop_port_code') }}" maxlength="4" name="drop_port_code" type="text" value="{{ old('drop_port_code', $end_user->drop_port_code) }}" {{ $end_user->addDisabledProp('drop_port_code') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="drop_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.drop_port_name') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="drop_port_name" class="form-control ime-active {{ has_error('drop_port_name') }}" maxlength="30" name="drop_port_name" type="text" value="{{ old('drop_port_name', $end_user->drop_port_name) }}" {{ $end_user->addDisabledProp('drop_port_name') }}>
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
              <input id="exchange_rate_port_code" class="form-control ime-inactive {{ has_error('exchange_rate_port_code') }}" maxlength="4" name="exchange_rate_port_code" type="text" value="{{ old('exchange_rate_port_code', $end_user->exchange_rate_port_code) }}" {{ $end_user->addDisabledProp('exchange_rate_port_code') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="exchange_rate_port_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.exchange_rate_port_name') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="exchange_rate_port_name" class="form-control ime-active {{ has_error('exchange_rate_port_name') }}" maxlength="30" name="exchange_rate_port_name" type="text" value="{{ old('exchange_rate_port_name', $end_user->exchange_rate_port_name) }}" {{ $end_user->addDisabledProp('exchange_rate_port_name') }}>
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
              <input id="lot_managing_target_flag" name="lot_managing_target_flag" type="checkbox" value="1" {{ is_checked(1, old('lot_managing_target_flag', $end_user->lot_managing_target_flag)) }} {{ $end_user->addDisabledProp('lot_managing_target_flag') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="end_user_remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.end_user_remark') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="end_user_remark" class="form-control ime-active {{ has_error('end_user_remark') }}" maxlength="50" name="end_user_remark" type="text" value="{{ old('end_user_remark', $end_user->end_user_remark) }}" {{ $end_user->addDisabledProp('end_user_remark') }}>
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
              <input id="end_user_request_number" class="form-control ime-inactive {{ has_error('end_user_request_number') }}" maxlength="5" name="end_user_request_number" type="text" value="{{ old('end_user_request_number', $end_user->end_user_request_number) }}" {{ $end_user->addDisabledProp('end_user_request_number') }}>
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
              <select id="statement_of_delivery_remark_class" class="form-control {{ has_error('statement_of_delivery_remark_class') }}" name="statement_of_delivery_remark_class" required {{ $end_user->addDisabledProp('statement_of_delivery_remark_class') }}>
                @foreach ($statement_of_delivery_remark_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, $end_user->statement_of_delivery_remark_class) }}>{{ $label }}</option>
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
              <select id="statement_of_delivery_buyer_remark_class" class="form-control {{ has_error('statement_of_delivery_buyer_remark_class') }}" name="statement_of_delivery_buyer_remark_class" required {{ $end_user->addDisabledProp('statement_of_delivery_buyer_remark_class') }}>
                <option value=""></option>
                @foreach ($statement_of_delivery_buyer_remark_class_list as $label => $value)
                <option value="{{ $value }}" {{ is_selected($value, old('statement_of_delivery_buyer_remark_class', $end_user->statement_of_delivery_buyer_remark_class)) }}>{{ $label }}</option>
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
              <input id="export_target_flag" name="export_target_flag" type="checkbox" value="1" {{ is_checked(1, old('export_target_flag', $end_user->export_target_flag)) }} {{ $end_user->addDisabledProp('export_target_flag') }}>
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
              <input id="group_company_flag" name="group_company_flag" type="checkbox" value="1" {{ is_checked(1, old('group_company_flag', $end_user->group_company_flag)) }} {{ $end_user->addDisabledProp('group_company_flag') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="company_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.company_code') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="company_code" class="form-control ime-inactive {{ has_error('company_code') }}" maxlength="50" name="company_code" type="text" value="{{ old('company_code', $end_user->company_code) }}" {{ $end_user->addDisabledProp('company_code') }}>
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
              <input id="company_name" class="form-control ime-active {{ has_error('company_name') }}" maxlength="50" name="company_name" type="text" value="{{ old('company_name', $end_user->company_name) }}" {{ $end_user->addDisabledProp('company_name') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="company_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.company_abbreviation') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="company_abbreviation" class="form-control ime-active {{ has_error('company_abbreviation') }}" maxlength="20" name="company_abbreviation" type="text" value="{{ old('company_abbreviation', $end_user->company_abbreviation) }}" {{ $end_user->addDisabledProp('company_abbreviation') }}>
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
              <input id="company_name_kana" class="form-control ime-active {{ has_error('company_name_kana') }}" maxlength="30" name="company_name_kana" type="text" value="{{ old('company_name_kana', $end_user->company_name_kana) }}" {{ $end_user->addDisabledProp('company_name_kana') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="company_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.company_name_english') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="company_name_english" class="form-control ime-inactive {{ has_error('company_name_english') }}" maxlength="50" name="company_name_english" type="text" value="{{ old('company_name_english', $end_user->company_group_name_english) }}" {{ $end_user->addDisabledProp('company_group_name_english') }}>
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
              <input id="company_group_code" class="form-control ime-inactive {{ has_error('company_group_code') }}" maxlength="8" name="company_group_code" type="text" value="{{ old('company_group_code', $end_user->company_group_code) }}" {{ $end_user->addDisabledProp('company_group_code') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="company_group_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              {{ __('view.master.end_users.company_group_name') }}
            </label>
            <div class="col-md-7 col-sm-7">
              <input id="company_group_name" class="form-control ime-active {{ has_error('company_group_name') }}" maxlength="40" name="company_group_name" type="text" value="{{ old('company_group_name', $end_user->company_group_name) }}" {{ $end_user->addDisabledProp('company_group_name') }}>
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
              <input id="company_group_name_english" class="form-control ime-inactive {{ has_error('company_group_name_english') }}" maxlength="8" name="company_group_name_english" type="text" value="{{ old('company_group_name_english', $end_user->company_group_name_english) }}" {{ $end_user->addDisabledProp('company_group_name_english') }}>
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="can_display" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
              {{ __('view.master.global.can_display') }}
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-7 col-sm-7">
              @foreach ($can_display_list as $label => $value)
              <label class="radio-inline">
                <input type="radio" name="can_display" id="can_display_{{ $value }}" value="{{ $value }}" {{ is_checked($value, old('can_display', $end_user->can_display)) }}>{{ $label }}
              </label>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      @include('_include.edit_reserved_form', ['master' => $end_user])
      <input name="updated_at" type="hidden" value="{{ $end_user->updated_at->format('Y-m-d H:i:s') }}">
      {{ method_field('PATCH') }}
      {{ csrf_field() }}

      @canSave(Auth::user(), route_relatively('master.end_users.index'))
      <input id="can-save-data" type="hidden" value="1">
      @else
      <input id="can-save-data" type="hidden" value="0">
      @endcanSave
    </form>
  </div>
</div>
@endsection
