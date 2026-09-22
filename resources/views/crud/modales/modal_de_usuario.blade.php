@php
  $userType = match ((int) auth()->user()->tipo_usuario) {
      1 => 'Administrador',
      3 => 'Profesor',
      default => 'Usuario',
  };
@endphp

<div aria-hidden="true" aria-labelledby="userInfoModalLabel" class="modal fade" id="userInfoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content user-modal">
      <div class="modal-header user-modal-header">
        <div>
          <span class="loan-eyebrow">Cuenta activa</span>
          <h2 class="modal-title h4 mb-0 mt-1" id="userInfoModalLabel">Informacion de usuario</h2>
        </div>
        <button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
      </div>
      <div class="modal-body p-4">
        <div class="user-profile-summary">
          @if (auth()->user()->imagen_perfil)
            <img alt="Foto de {{ auth()->user()->name }}" class="user-avatar" src="{{ asset('storage/' . auth()->user()->imagen_perfil) }}">
          @else
            <span class="user-avatar user-avatar-placeholder">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
          @endif
          <div>
            <h3 class="h5 mb-1">{{ auth()->user()->name }}</h3>
            <p class="mb-0 text-muted">{{ $userType }}</p>
          </div>
        </div>

        <dl class="user-details mb-0 mt-4">
          <div>
            <dt>Nombre completo</dt>
            <dd>{{ auth()->user()->name }}</dd>
          </div>
          <div>
            <dt>Usuario</dt>
            <dd>{{ auth()->user()->usuario }}</dd>
          </div>
          <div>
            <dt>Correo electronico</dt>
            <dd>{{ auth()->user()->email }}</dd>
          </div>
          <div>
            <dt>Tipo de usuario</dt>
            <dd>{{ $userType }}</dd>
          </div>
          <div>
            <dt>Miembro desde</dt>
            <dd>{{ optional(auth()->user()->created_at)->format('d/m/Y') ?? 'No disponible' }}</dd>
          </div>
        </dl>
      </div>
      <div class="modal-footer justify-content-between">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cerrar</button>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button class="btn btn-coral" type="submit">Cerrar sesion</button>
        </form>
      </div>
    </div>
  </div>
</div>