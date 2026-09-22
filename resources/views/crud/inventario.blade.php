@extends('loyauts.main')

@section('title', 'Panel de inventario')

@section('contenido')
<div class="loan-app">
	<header class="loan-hero">
		<div class="container py-5">
			<span class="loan-eyebrow">Panel de inventario</span>
			<h1 class="display-5 fw-bold mt-2">Recursos disponibles</h1>
			<p class="lead mb-0">Consulta y organiza el equipo disponible para prestamos.</p>
		</div>
	</header>

	<main class="container py-4 py-lg-5">
		<div aria-label="Seleccionar catalogo" class="inventory-mode-switcher mb-4" role="group">
			<button class="inventory-mode-button active" id="inventoryModeAulas" type="button">Aulas</button>
			<button class="inventory-mode-button" id="inventoryModeResources" type="button">Recursos</button>
			<button class="inventory-mode-create" id="createInventoryItemButton" type="button"><span aria-hidden="true">+</span> Crear</button>
		</div>

		@include('crud.modales.crear_aulas')
		@include('crud.modales.crear_inventario')
		@include('crud.modales.editar_aulas_recursos')

		<section class="loan-panel mb-4" aria-labelledby="inventory-filters-title">
			<div class="loan-panel-heading">
				<div>
					<span class="loan-eyebrow">Catalogo</span>
					<h2 class="h4 mb-0 mt-1" id="inventory-filters-title">Filtrar aulas</h2>
				</div>
				<button class="btn btn-link text-decoration-none" type="reset">Limpiar filtros</button>
			</div>
			<div class="p-4">
				<div class="row g-3">
					<div class="col-lg-8">
						<label class="form-label" for="inventorySearch">Buscar aula</label>
						<input class="form-control" id="inventorySearch" placeholder="Numero o descripcion del aula" type="search">
					</div>
					<div class="col-lg-4">
						<label class="form-label" for="inventoryAvailability">Disponibilidad</label>
						<select class="form-select" id="inventoryAvailability">
							<option>Todos</option>
							<option>Disponible</option>
							<option>No disponible</option>
						</select>
					</div>
					<div class="col-lg-4" id="buildingFilterGroup">
						<label class="form-label" for="buildingFilter">Edificio</label>
						<div class="input-group">
							<select class="form-select" id="buildingFilter"><option value="">Todos los edificios</option></select>
							<button class="btn btn-outline-primary" id="editBuildingButton" type="button">Editar</button>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="loan-panel overflow-hidden" aria-labelledby="inventory-title">
			<div class="loan-panel-heading">
				<div>
							<span class="loan-eyebrow">Catalogo</span>
							<h2 class="h4 mb-0 mt-1" id="inventory-title">Aulas registradas</h2>
				</div>
						<span class="badge loan-badge" id="inventoryCount">0 aulas</span>
			</div>
			<div class="table-responsive">
				<table class="table loan-table mb-0">
						<thead id="classroomsTableHead"><tr><th>Numero</th><th>Edificio</th><th>Descripcion</th><th>Acciones</th></tr></thead>
						<tbody id="classroomsTableBody">
							<tr><td class="loan-empty text-center" colspan="4">No hay aulas registradas.</td></tr>
						</tbody>
						<thead class="d-none" id="resourcesTableHead"><tr><th>Nombre</th><th>Cantidad</th><th>Descripcion</th><th>Acciones</th></tr></thead>
						<tbody class="d-none" id="resourcesTableBody">
							<tr><td class="loan-empty text-center" colspan="4">No hay recursos registrados.</td></tr>
						</tbody>
				</table>
			</div>
		</section>
	</main>
</div>

@endsection