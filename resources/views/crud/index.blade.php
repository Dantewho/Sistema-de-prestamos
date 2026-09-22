@extends('loyauts.main')

@section('title', 'Mis prestamos')

@section('contenido')
<div class="loan-app">
  @include('crud.modales.crearPrestamo')

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
              <option>Finalizados</option>
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
              <th>Rol</th>
              <th>Fecha</th>
              <th>Identificacion</th>
              <th>Prestado por</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="requestsTableBody">
            <tr>
              <td class="loan-empty text-center" colspan="7">
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