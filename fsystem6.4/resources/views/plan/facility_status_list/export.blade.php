<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
</head>
<body>
  <table>
    @foreach ($sheet_list as $row_list)
      <tr>
        @foreach ($row_list as $cell)
          <td>{{ $cell }}</td>
        @endforeach
      </tr>
    @endforeach
  </table>
</body>
</html>
