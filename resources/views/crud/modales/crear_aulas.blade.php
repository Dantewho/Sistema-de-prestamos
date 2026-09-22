<div aria-hidden="true" aria-labelledby="createClassroomModalLabel" class="modal fade" id="createClassroomModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header">
				<div>
					<span class="loan-eyebrow">Catalogo de aulas</span>
					<h2 class="modal-title h4 mb-0 mt-1" id="createClassroomModalLabel">Agregar aula</h2>
				</div>
				<button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
			</div>
			<form id="createClassroomForm">
				<div class="modal-body p-4">
					<div class="alert alert-danger d-none" id="classroomFormError" role="alert"></div>
					<div class="mb-3">
						<label class="form-label" for="classroomBuilding">Edificio</label>
						<select class="form-select" id="classroomBuilding" name="edificio_id" required>
							<option value="">Cargando edificios...</option>
							<option value="new">Crear nuevo edificio</option>
						</select>
						<button class="btn btn-link px-0" id="openBuildingModalButton" type="button">+ Crear edificio sin aula</button>
					</div>
					<div class="border rounded p-3 mb-3 d-none" id="newBuildingFields">
						<p class="fw-semibold mb-3">Nuevo edificio</p>
						<label class="form-label" for="newBuildingName">Nombre</label>
						<input class="form-control mb-3" id="newBuildingName" maxlength="10" name="nombre_edificio" placeholder="Ej. Edificio A" type="text">
						<label class="form-label" for="newBuildingDescription">Descripcion</label>
						<textarea class="form-control" id="newBuildingDescription" name="descripcion_edificio" rows="2"></textarea>
					</div>
					<div class="mb-3">
						<label class="form-label" for="classroomNumber">Numero de aula</label>
						<input class="form-control" id="classroomNumber" maxlength="10" name="numero" placeholder="Ej. A-101" required type="text">
					</div>
					<div>
						<label class="form-label" for="classroomDescription">Descripcion</label>
						<textarea class="form-control" id="classroomDescription" name="descripcion" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
					<button class="btn btn-coral" id="saveClassroomButton" type="submit">Guardar aula</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div aria-hidden="true" aria-labelledby="createBuildingModalLabel" class="modal fade" id="createBuildingModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header">
				<div>
					<span class="loan-eyebrow">Catalogo de edificios</span>
					<h2 class="modal-title h4 mb-0 mt-1" id="createBuildingModalLabel">Nuevo edificio</h2>
				</div>
				<button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
			</div>
			<form id="createBuildingForm">
				<div class="modal-body p-4">
					<div class="alert alert-danger d-none" id="buildingFormError" role="alert"></div>
					<label class="form-label" for="buildingName">Nombre</label>
					<input class="form-control mb-3" id="buildingName" maxlength="10" required type="text">
					<label class="form-label" for="buildingDescription">Descripcion</label>
					<textarea class="form-control" id="buildingDescription" rows="3"></textarea>
				</div>
				<div class="modal-footer">
					<button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
					<button class="btn btn-coral" type="submit">Guardar edificio</button>
				</div>
			</form>
		</div>
	</div>
</div>
