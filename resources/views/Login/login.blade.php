@extends('loyauts.main')

@section('title', 'Iniciar sesión')

@section('contenido')
<main class="auth-page">
	<section class="auth-card" aria-labelledby="login-title">
		<p class="auth-kicker">Sistema de préstamos</p>
		<h1 id="login-title">Inicia sesión</h1>
		<p class="auth-subtitle">Accede para solicitar aulas o equipo audiovisual.</p>

		@if ($errors->any())
			<div class="auth-errors" role="alert">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form class="auth-form" method="POST" action="{{ route('login.store') }}">
			@csrf
			<label for="email">
				Correo electrónico
				<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
			</label>

			<label for="password">
				Contraseña
				<input id="password" name="password" type="password" autocomplete="current-password" required>
			</label>

			<label class="auth-checkbox" for="remember">
				<input id="remember" name="remember" type="checkbox" value="1">
				<span>Mantener la sesión iniciada</span>
			</label>

			<button class="auth-submit" type="submit">Entrar</button>
		</form>

		<p class="auth-footer">¿No tienes cuenta? <a class="auth-link" href="{{ route('register') }}">Regístrate</a></p>
	</section>
</main>
@endsection
