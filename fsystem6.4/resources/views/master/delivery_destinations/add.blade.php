@extends('layout')

@section('title')
{{ __('view.master.delivery_destinations.add') }}
@endsection

@section('menu-section')
{{ __('view.index.master-maintenance') }}
@endsection

@section('breadcrumbs')
  <li>
    <a href="{{ route('master.delivery_destinations.index') }}">{{ __('view.master.delivery_destinations.index') }}</a>
  </li>
  <li>{{ __('view.master.delivery_destinations.add') }}</li>
@endsection

@section('content')
<form id="save-delivery_destination-form" class="form-horizontal basic-form save-data-form" action="{{ route('master.delivery_destinations.create') }}" method="POST">
  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <a class="btn btn-default btn-lg back-button" href="{{ route('master.delivery_destinations.index') }}">
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
        <label for="delivery_destination_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination_code') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_code"
            class="form-control ime-inactive {{ has_error('delivery_destination_code') }}"
            maxlength="{{ $delivery_destination_code->getMaxLength() }}"
            name="delivery_destination_code"
            type="text"
            value="{{ old('delivery_destination_code') }}"
            data-toggle="tooltip"
            title="{{ replace_el($delivery_destination_code->getHelpText()) }}"
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
        <label for="delivery_destination_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination_name') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_name"
            class="form-control ime-active {{ has_error('delivery_destination_name') }}"
            maxlength="50"
            name="delivery_destination_name"
            type="text"
            value="{{ old('delivery_destination_name') }}"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="delivery_destination_name2" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.delivery_destination_name2') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_name2"
            class="form-control ime-active {{ has_error('delivery_destination_name2') }}"
            maxlength="50"
            name="delivery_destination_name2"
            type="text"
            value="{{ old('delivery_destination_name2') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="delivery_destination_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination_abbreviation') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_abbreviation"
            class="form-control ime-active {{ has_error('delivery_destination_abbreviation') }}"
            maxlength="20"
            name="delivery_destination_abbreviation"
            type="text"
            value="{{ old('delivery_destination_abbreviation') }}"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="delivery_destination_name_kana" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination_name_kana') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_name_kana"
            class="form-control ime-active {{ has_error('delivery_destination_name_kana') }}"
            maxlength="30"
            name="delivery_destination_name_kana"
            type="text"
            value="{{ old('delivery_destination_name_kana') }}"
            required>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="delivery_destination_name_english" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.delivery_destination_name_english') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="delivery_destination_name_english"
            class="form-control ime-inactive {{ has_error('delivery_destination_name_english') }}"
            maxlength="65"
            name="delivery_destination_name_english"
            type="text"
            value="{{ old('delivery_destination_name_english') }}">
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
          <input
            id="country_code"
            class="form-control text-center ime-inactive {{ has_error('country_code') }}"
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
          <input
            id="postal_code"
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
          <select id="prefecture_code" class="form-control {{ has_error('prefecture_code') }}" name="prefecture_code" data-toggle="tooltip" title="{{ $prefecture_code->getHelpText() }}" required>
            <option value=""></option>
            @foreach ($prefecture_code->all() as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('prefecture_code')) }}>{{ $value }}: {{ $label }}</option>
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
          <input
            id="abroad_address"
            class="form-control ime-inactive {{ has_error('abroad_address') }}"
            maxlength="50"
            name="abroad_address"
            type="text"
            value="{{ old('abroad_address') }}">
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
          <input
            id="abroad_address2"
            class="form-control ime-inactive {{ has_error('abroad_address2') }}"
            maxlength="50"
            name="abroad_address2"
            type="text"
            value="{{ old('abroad_address2') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="abroad_address3" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.abroad_address3') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="abroad_address3"
            class="form-control ime-inactive {{ has_error('abroad_address3') }}"
            maxlength="50"
            name="abroad_address3"
            type="text"
            value="{{ old('abroad_address3') }}">
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
          <input
            id="phone_number"
            class="form-control ime-inactive {{ has_error('phone_number') }}"
            maxlength="20"
            name="phone_number"
            type="text"
            value="{{ old('phone_number') }}"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="extension_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.extension_number') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="extension_number"
            class="form-control ime-inactive {{ has_error('extension_number') }}"
            maxlength="15"
            name="extension_number"
            type="text"
            value="{{ old('extension_number') }}">
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
          <input
            id="fax_number"
            class="form-control ime-inactive {{ has_error('fax_number') }}"
            maxlength="15"
            name="fax_number"
            type="text"
            value="{{ old('fax_number') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="mail_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.global.mail_address') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="mail_address"
            class="form-control ime-inactive {{ has_error('mail_address') }}"
            maxlength="250"
            name="mail_address"
            type="email"
            value="{{ old('mail_address') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="staff_abbreviation" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.staff_abbreviation') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="staff_abbreviation"
            class="form-control ime-active {{ has_error('staff_abbreviation') }}"
            maxlength="10"
            name="staff_abbreviation"
            type="text"
            value="{{ old('staff_abbreviation') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="statement_of_delivery_output_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.statement_of_delivery_output_class') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="statement_of_delivery_output_class" class="form-control {{ has_error('statement_of_delivery_output_class') }}" name="statement_of_delivery_output_class" required>
            <option value=""></option>
            @foreach ($statement_of_delivery_output_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('statement_of_delivery_output_class')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="statement_of_delivery_message" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.statement_of_delivery_message') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="statement_of_delivery_message"
            class="form-control ime-active {{ has_error('statement_of_delivery_message') }}"
            maxlength="40"
            name="statement_of_delivery_message"
            type="text"
            value="{{ old('statement_of_delivery_message') }}">
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="shipping_label_unnecessary_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.shipping_label_unnecessary_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="shipping_label_unnecessary_flag"
            name="shipping_label_unnecessary_flag"
            type="checkbox"
            required="required"
            value="1"
            {{ is_checked(1, old('shipping_label_unnecessary_flag', 0)) }}>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="export_target_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.export_target_flag') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="export_target_flag" name="export_target_flag" type="checkbox" required="required" value="1" {{ is_checked(1, old('export_target_flag', 0)) }}>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="shipment_way_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.shipment_way_class') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="shipment_way_class" class="form-control {{ has_error('shipment_way_class') }}" name="shipment_way_class">
            <option value=""></option>
            @foreach ($shipment_way_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('shipment_way_class')) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="delivery_destination_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.delivery_destination_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <select id="delivery_destination_class" class="form-control {{ has_error('delivery_destination_class') }}" name="delivery_destination_class" required>
            <option value=""></option>
            @foreach ($delivery_destination_class_list as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('delivery_destination_class', 1)) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <set-transport-company-form
    :delivery-destination="{{ json_encode(new \stdClass()) }}"
    :transport-companies="{{ $transport_companies }}"
    :old-params="{{ json_encode(old() ?: new \stdClass()) }}"
    :errors="{{ $errors }}">
  </set-transport-company-form>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="collection_request_remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.collection_request_remark') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="collection_request_remark"
            class="form-control ime-active {{ has_error('collection_request_remark') }}"
            maxlength="50"
            name="collection_request_remark"
            type="text"
            value="{{ old('collection_request_remark') }}">
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="end_user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.end_users.end_user') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <search-master target="end_user" code="{{ old('end_user_code') }}" name="{{ old('end_user_name') }}">
          </search-master>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="fsystem_statement_of_delivery_output_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.fsystem_statement_of_delivery_output_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-5 col-sm-7">
          <select id="fsystem_statement_of_delivery_output_class" class="form-control {{ has_error('fsystem_statement_of_delivery_output_class') }}" name="fsystem_statement_of_delivery_output_class" required>
            <option value=""></option>
            @foreach ($fsystem_statement_of_delivery_output_class->all() as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('fsystem_statement_of_delivery_output_class', $fsystem_statement_of_delivery_output_class->getDefaultValue())) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <div class="row form-group">
        <label for="statement_of_shipment_output_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
          {{ __('view.master.delivery_destinations.statement_of_shipment_output_class') }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-4 col-sm-7">
          <select id="statement_of_shipment_output_class" class="form-control {{ has_error('statement_of_shipment_output_class') }}" name="statement_of_shipment_output_class" required>
            <option value=""></option>
            @foreach ($statement_of_shipment_output_class->all() as $label => $value)
            <option value="{{ $value }}" {{ is_selected($value, old('statement_of_shipment_output_class', $statement_of_shipment_output_class->getDefaultValue())) }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
      <div class="row form-group">
        <label for="needs_to_subtract_printing_delivery_date" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
          {{ __('view.master.delivery_destinations.needs_to_subtract_printing_delivery_date') }}
        </label>
        <div class="col-md-7 col-sm-7">
          <input id="needs_to_subtract_printing_delivery_date" name="needs_to_subtract_printing_delivery_date" type="checkbox" required="required" value="1" {{ is_checked(1, old('needs_to_subtract_printing_delivery_date', 0)) }}>
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
          <label class="radio-inline" data-toggle="tooltip" title="{{ replace_el($can_display->getHelpText()) }}">
            <input type="radio" name="can_display" value="{{ $value }}" {{ is_checked($value, old('can_display', 1)) }}>
            {{ $label }}
          </label>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  @include('_include.add_reserved_form')
  {{ csrf_field() }}
  {{ method_field('POST') }}
</form>
@endsection
