@extends('loyauts.main')

@section('title', 'Crear cuenta')

@section('contenido')
<main class="auth-page">
	<section class="auth-card" aria-labelledby="register-title">
		<p class="auth-kicker">Sistema de préstamos</p>
		<h1 id="register-title">Crea tu cuenta</h1>
		<p class="auth-subtitle">Regístrate para gestionar tus solicitudes.</p>

		@if ($errors->any())
			<div class="auth-errors" role="alert">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form class="auth-form" method="POST" action="{{ route('register.store') }}">
			@csrf
			<label for="name">
				Nombre completo
				<input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
			</label>

			<label for="usuario">
				Usuario
				<input id="usuario" name="usuario" type="text" value="{{ old('usuario') }}" autocomplete="username" required>
			</label>

			<label for="email">
				Correo electrónico
				<input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
			</label>

			<label for="password">
				Contraseña
				<input id="password" name="password" type="password" autocomplete="new-password" required>
			</label>

			<label for="password_confirmation">
				Confirmar contraseña
				<input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
			</label>

			<button class="auth-submit" type="submit">Crear cuenta</button>
		</form>

		<p class="auth-footer">¿Ya tienes cuenta? <a class="auth-link" href="{{ route('login') }}">Inicia sesión</a></p>
	</section>
</main>
@endsection
