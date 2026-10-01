@extends('loyauts.main')

@section('title', 'Mis prestamos')

@section('contenido')
<div class="loan-app">
  @include('crud.modales.crearPrestamo')

  <div aria-hidden="true" aria-labelledby="loanDetailsModalLabel" class="modal fade" id="loanDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content loan-modal">
        <div class="modal-header loan-modal-header">
          <div>
            <span class="loan-eyebrow">Seguimiento</span>
            <h2 class="modal-title h4 mb-0 mt-1" id="loanDetailsModalLabel">Detalle del prestamo</h2>
          </div>
          <button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
        </div>
        <div class="modal-body p-4" id="loanDetailsContent"></div>
      </div>
    </div>
  </div>

  <header class="loan-hero">
    <div class="container py-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <span class="loan-eyebrow">Panel de prestamos</span>
          <h1 class="display-5 fw-bold mt-2">Que necesitas hoy?</h1>
          <p class="lead mb-0">Solicita aulas y equipo audiovisual de forma sencilla y consulta tus prestamos en un solo lugar.</p>
        </div>
      </div>
    </div>
  </header>

  <main class="container py-4 py-lg-5" id="prestamos">
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <article class="loan-stat h-100">
          <span class="loan-stat-icon">#</span>
          <div><p class="loan-stat-label">Prestamos totales</p><strong data-request-stat="total">0</strong></div>
        </article>
      </div>
      <div class="col-md-4">
        <article class="loan-stat h-100">
          <span class="loan-stat-icon loan-stat-icon-success">&#10003;</span>
          <div><p class="loan-stat-label">Activos</p><strong data-request-stat="activa">0</strong></div>
        </article>
      </div>
      <div class="col-md-4">
        <article class="loan-stat h-100">
          <span class="loan-stat-icon loan-stat-icon-warning">!</span>
          <div><p class="loan-stat-label">Pendientes</p><strong data-request-stat="pendiente">0</strong></div>
        </article>
      </div>
    </div>
    <!-- 
      IMPORTACION DE DATOS
    -->
     <section class="loan-panel mb-4" aria-labelledby="user-import-title">
            <div class="loan-panel-heading">
                <div><span class="loan-eyebrow">Carga de datos</span><h2 class="h4 mb-0 mt-1" id="user-import-title">Importar perfiles</h2></div>
                <a href="{{ route('SolicitudExport.exportar') }}" class="btn btn-success">Exportar Excel</a>

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
                <form action="{{ route('SolicitudImport.importar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-9">
                            <label class="form-label" for="archivo">Archivo Excel o CSV</label>
                            <input class="form-control" id="archivo" name="archivo" type="file" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Agrega el acrchivo Excel con el formato correcto.</div>
                        </div>
                        <div class="col-lg-3">
                            <button class="btn btn-coral w-100" type="submit">Importar Solicitudes</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>


    <!-- 
      AQUI TERMINA EL FORMULARIO DE EXPORTACION/IMPORTACION DE DATOS
    -->
    <section class="loan-panel mb-4" aria-labelledby="filters-title">
      <div class="loan-panel-heading">
        <div>
          <span class="loan-eyebrow">Encuentra lo que buscas</span>
          <h2 class="h4 mb-0 mt-1" id="filters-title">Filtrar prestamos</h2>
        </div>
        <button class="btn btn-link text-decoration-none" id="clearLoanFilters" type="button">Limpiar filtros</button>
      </div>
      <div class="p-4">
        <div class="row g-3">
          <div class="col-lg-5">
            <label class="form-label" for="nombre">Buscar producto</label>
            <div class="input-group">
              <span class="input-group-text">&#128269;</span>
              <input class="form-control" id="nombre" name="nombre" placeholder="Nombre del aula o equipo" type="search">
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <label class="form-label" for="estado">Estado</label>
            <select class="form-select" id="estado">
              <option>Todos</option>
              <option>Pendientes</option>
              <option>Activos</option>
              <option>Finalizada</option>
              <option>Cancelados</option>
            </select>
          </div>
          <div class="col-md-6 col-lg-4">
            <label class="form-label" for="tipo">Tipo de recurso</label>
            <select class="form-select" id="tipo">
              <option>Todos</option>
              <option>Aulas</option>
              <option>Inventario</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <section class="loan-panel overflow-hidden" aria-labelledby="requests-title">
      <div class="loan-panel-heading">
        <div>
          <span class="loan-eyebrow">Seguimiento</span>
          <h2 class="h4 mb-0 mt-1" id="requests-title">Prestamos registrados</h2>
        </div>
        <div class="align-items-center d-flex gap-3">
          <span class="badge loan-badge" id="requestCount">0 resultados</span>
          <button class="btn btn-coral" id="createLoanButton" type="button">+ Crear prestamo</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table loan-table mb-0">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Equipo/Aula</th>
              <th>Usuario</th>
              <th>Fecha</th>
              <th>Identificacion</th>
              <th>Prestado por</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="requestsTableBody">
            <tr>
              <td class="loan-empty text-center" colspan="8">
                <div class="loan-empty-icon">+</div>
                <h3 class="h5 mt-3">Aun no tienes prestamos</h3>
                <p class="mb-3">Crea tu primera solicitud para comenzar a gestionar tus recursos.</p>
                <button class="btn btn-primary" id="createLoanEmptyButton" type="button">Crear primer prestamo</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>

@endsection