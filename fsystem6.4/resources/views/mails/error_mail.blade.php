{!!config('app.name')!!}

{!!$text!!}
@if (count($validation_error_messages) !== 0)
@foreach ($validation_error_messages as $key => $value)

----- {{$key}}行目 -----
@foreach ($value as $validation_message)
・{!!$validation_message!!}
@endforeach
@endforeach
@endif