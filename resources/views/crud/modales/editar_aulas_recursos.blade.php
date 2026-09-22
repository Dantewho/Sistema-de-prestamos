<div aria-hidden="true" aria-labelledby="editCatalogModalLabel" class="modal fade" id="editCatalogModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header">
				<div>
					<span class="loan-eyebrow">Editar catalogo</span>
					<h2 class="modal-title h4 mb-0 mt-1" id="editCatalogModalLabel">Editar registro</h2>
				</div>
				<button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
			</div>
			<form id="editCatalogForm">
				<div class="modal-body p-4">
					<div class="alert alert-danger d-none" id="editCatalogFormError" role="alert"></div>
					<input id="editCatalogId" type="hidden">
					<input id="editCatalogType" type="hidden">
					<div class="mb-3 d-none" id="editBuildingFieldsGroup">
						<label class="form-label" for="editBuildingName">Nombre del edificio</label>
						<input class="form-control mb-3" id="editBuildingName" maxlength="10" type="text">
						<label class="form-label" for="editBuildingDescription">Descripcion del edificio</label>
						<textarea class="form-control" id="editBuildingDescription" rows="3"></textarea>
					</div>
					<div class="mb-3" id="editClassroomBuildingGroup">
						<label class="form-label" for="editClassroomBuilding">Edificio</label>
						<select class="form-select" id="editClassroomBuilding"></select>
					</div>
					<div class="mb-3" id="editClassroomNumberGroup">
						<label class="form-label" for="editClassroomNumber">Numero de aula</label>
						<input class="form-control" id="editClassroomNumber" maxlength="10" type="text">
					</div>
					<div class="mb-3" id="editInventoryNameGroup">
						<label class="form-label" for="editInventoryName">Nombre del recurso</label>
						<input class="form-control" id="editInventoryName" maxlength="100" type="text">
					</div>
					<div class="mb-3" id="editInventoryQuantityGroup">
						<label class="form-label" for="editInventoryQuantity">Cantidad</label>
						<input class="form-control" id="editInventoryQuantity" min="0" type="number">
					</div>
					<label class="form-label" for="editCatalogDescription">Descripcion</label>
					<textarea class="form-control" id="editCatalogDescription" rows="3"></textarea>
				</div>
				<div class="modal-footer">
					<button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
					<button class="btn btn-coral" type="submit">Guardar cambios</button>
				</div>
			</form>
		</div>
	</div>
</div>
