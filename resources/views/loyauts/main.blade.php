<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/css/index.css', 'resources/js/app.js', 'resources/js/crud/index.js', 'resources/js/crud/inventario.js', 'resources/js/crud/usuarios.js'])

    <title>@yield('title', 'Sistema de prestamos')</title>
  </head>
  <body>
    @yield('Contenido del usuario')
    @auth
      <div class="app-shell">
        <header class="app-header">
          <div class="container-fluid px-3 px-lg-4">
            <div class="align-items-center d-flex justify-content-between py-3">
              <a class="app-brand" href="{{ route('prestamos.index') }}">
                <span class="app-brand-mark">SP</span>
                <span>Sistema de prestamos</span>
              </a>
              <button class="app-user-button" id="userPanelButton" type="button">
                <span class="app-user-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
              </button>
            </div>
            <nav aria-label="Paneles principales" class="app-panel-nav pb-3">
              <a class="app-panel-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">Panel de usuarios</a>
              <a class="app-panel-link {{ request()->routeIs('inventario.panel') ? 'active' : '' }}" href="{{ route('inventario.panel') }}">Panel de inventario</a>
              <a class="app-panel-link {{ request()->routeIs('prestamos.index') ? 'active' : '' }}" href="{{ route('prestamos.index') }}">Panel de prestamos</a>
            </nav>
          </div>
        </header>
        @include('crud.modales.modal_de_usuario')
        @yield('contenido')
      </div>
    @else
      @yield('contenido')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  </body>
</html>