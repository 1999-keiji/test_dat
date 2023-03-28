<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <div class="row form-group">
      <label for="remark" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __('view.global.remark') }}
      </label>
      <div class="col-md-7 col-sm-7">
        <textarea id="remark" class="form-control {{ has_error('remark') }}" name="remark" rows="3">{{ old('remark') }}</textarea>
      </div>
    </div>
  </div>
</div>

@foreach (range(1, (int)(config('settings.number_of_reserved_item') / 2)) as $num)
@php
$idx = $num * 2 - 1
@endphp
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <div class="row form-group">
      <label for="reserved_text{{ $idx }}" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __("view.global.reserved_text{$idx}") }}
      </label>
      <div class="col-md-7 col-sm-7">
        <input id="reserved_text{{ $idx }}" class="form-control {{ has_error("reserved_text{$idx}") }}" maxlength="200" name="reserved_text{{ $idx }}" type="text" value="{{ old("reserved_text{$idx}") }}">
      </div>
    </div>
  </div>
  @php
  $idx = $num * 2
  @endphp
  <div class="col-md-5 col-sm-5 col-xs-6">
    <div class="row form-group">
      <label for="reserved_text{{ $idx }}" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __("view.global.reserved_text{$idx}") }}
      </label>
      <div class="col-md-7 col-sm-7">
        <input id="reserved_text{{ $idx }}" class="form-control {{ has_error("reserved_text{$idx}") }}" maxlength="200" name="reserved_text{{ $idx }}" type="text" value="{{ old("reserved_text{$idx}") }}">
      </div>
    </div>
  </div>
</div>
@endforeach

@foreach (range(1, (int)(config('settings.number_of_reserved_item') / 2)) as $num)
@php
$idx = $num * 2 - 1
@endphp
<div class="row">
  <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
    <div class="row form-group">
      <label for="reserved_number{{ $idx }}" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __("view.global.reserved_number{$idx}") }}
      </label>
      <div class="col-md-7 col-sm-7">
        <input-number-with-formatter
          max-length="19"
          attr-name="reserved_number{{ $idx }}"
          value="{{ old("reserved_number{$idx}") }}"
          decimals="5"
          :has-error="{{ json_encode(has_error("reserved_number{$idx}") !== '') }}">
        </input-number-with-formatter>
      </div>
    </div>
  </div>
  @php
  $idx = $num * 2
  @endphp
  <div class="col-md-5 col-sm-5 col-xs-6">
    <div class="row form-group">
      <label for="reserved_number{{ $idx }}" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
        {{ __("view.global.reserved_number{$idx}") }}
      </label>
      <div class="col-md-7 col-sm-7">
        <input-number-with-formatter
          max-length="19"
          attr-name="reserved_number{{ $idx }}"
          value="{{ old("reserved_number{$idx}") }}"
          decimals="5"
          :has-error="{{ json_encode(has_error("reserved_number{$idx}") !== '') }}">
        </input-number-with-formatter>
      </div>
    </div>
  </div>
</div>
@endforeach
