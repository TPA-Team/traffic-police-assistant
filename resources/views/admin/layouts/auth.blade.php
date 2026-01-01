<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Admin Login')</title>

  <!-- Bootstrap & AdminLTE -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body {
      background-color: #f4f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .auth-box {
      width: 100%;
      max-width: 400px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      padding: 30px;
    }
  </style>
</head>

<body>
  <div class="auth-box">
    @yield('content')
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('dist/js/adminlte.js') }}"></script>
  @yield('scripts')
</body>
</html>
