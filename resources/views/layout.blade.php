<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'CookBook') - 菜谱搜索</title>

  <!-- Styles -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">

  @yield('css')

</head>

<body>
<div id="app" class="{{ route_class() }}-page">

  @include('partials.header')

  <div class="container">

    @yield('content')

  </div>

  @include('partials.footer')
</div>

<!-- Scripts -->
<script src="{{ mix('js/app.js') }}"></script>

@yield('js')
</body>

</html>
