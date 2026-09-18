<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema de préstamos')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body>
    @yield('contenido')
  </body>
</html>