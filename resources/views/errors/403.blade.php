<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Acceso denegado</title>
	@vite(['resources/css/app.css'])
</head>
<body>
	<main class="auth-page">
		<section aria-labelledby="access-denied-title" class="auth-card">
			<p class="auth-kicker">Error 403</p>
			<h1 id="access-denied-title">Acceso denegado</h1>
			<p class="auth-subtitle">Solo los administradores pueden acceder al sistema. Cierra esta sesión para regresar al login.</p>
			<form action="{{ route('logout') }}" method="POST">
				@csrf
				<button class="auth-submit" type="submit">Cerrar sesión y regresar al login</button>
			</form>
		</section>
	</main>
</body>
</html>