@extends('loyauts.main')

@section('title', 'Panel de usuarios')

@section('contenido')
<div class="loan-app">
    @include('crud.modales.crear_usuario')
    @include('crud.modales.editar_usuario')
    <header class="loan-hero">
        <div class="container py-5">
            <span class="loan-eyebrow">Panel de usuarios</span>
            <h1 class="display-5 fw-bold mt-2">Personas registradas</h1>
            <p class="lead mb-0">Consulta la informacion de los usuarios del sistema.</p>
        </div>
    </header>

    <main class="container py-4 py-lg-5">
        <section class="loan-panel mb-4" aria-labelledby="user-filters-title">
            <div class="loan-panel-heading">
                <div>
                    <span class="loan-eyebrow">Directorio</span>
                    <h2 class="h4 mb-0 mt-1" id="user-filters-title">Filtrar usuarios</h2>
                </div>
                <div><button class="btn btn-link text-decoration-none" id="clearUserFilters" type="button">Limpiar filtros</button><button class="btn btn-coral" id="createUserButton" type="button">+ Crear usuario</button></div>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <label class="form-label" for="userSearch">Buscar usuario</label>
                        <input class="form-control" id="userSearch" placeholder="Nombre, usuario o correo" type="search">
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="userType">Tipo de usuario</label>
                        <select class="form-select" id="userType"><option>Todos</option><option>Administrador</option><option>Profesor</option><option>Usuario</option></select>
                    </div>
                </div>
            </div>
        </section>
        <section class="loan-panel mb-4" aria-labelledby="user-import-title">
            <div class="loan-panel-heading">
                <div><span class="loan-eyebrow">Carga de datos</span><h2 class="h4 mb-0 mt-1" id="user-import-title">Importar perfiles</h2></div>
                <a href="{{ route('perfiles.exportar') }}" class="btn btn-success">Exportar Excel</a>

            </div>
            <div class="p-4">
                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('perfiles.importar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-9">
                            <label class="form-label" for="archivo">Archivo Excel o CSV</label>
                            <input class="form-control" id="archivo" name="archivo" type="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Usa las columnas name, usuario, email, tipo_usuario, imagen_perfil y password. La contraseña puede quedar vacía al actualizar; es obligatoria para usuarios nuevos.</div>
                        </div>
                        <div class="col-lg-3">
                            <button class="btn btn-coral w-100" type="submit">Importar perfiles</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <section class="loan-panel overflow-hidden" aria-labelledby="users-title">
            <div class="loan-panel-heading">
                <div><span class="loan-eyebrow">Directorio</span><h2 class="h4 mb-0 mt-1" id="users-title">Usuarios registrados</h2></div>
                <span class="badge loan-badge" id="userCount">0 usuarios</span>
            </div>
            <div class="table-responsive">
                <table class="table loan-table mb-0">
                    <thead><tr><th>ID</th><th>Usuario</th><th>Email</th><th>Tipo</th><th>Solicitudes</th><th>Acciones</th></tr></thead>
                    <tbody id="usersTableBody"><tr><td class="loan-empty text-center" colspan="6">Cargando usuarios...</td></tr></tbody>
                </table>
            </div>
        </section>
    </main>
</div>
@endsection