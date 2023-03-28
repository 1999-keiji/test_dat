@if (session()->has('alert'))
<div class="row">
  <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
    <div class="alert alert-{{ session('alert.class') }}">
      <p>{{ session('alert.message') }}</p>
    </div>
  </div>
</div>
@endif

@if ($errors->any())
<div class="row">
  <div class="col-md-10 col-sm-10 col-md-offset-1 col-sm-offset-1">
    <div class="alert alert-danger">
      <p>入力エラーがあります。</p>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
</div>
@endif
