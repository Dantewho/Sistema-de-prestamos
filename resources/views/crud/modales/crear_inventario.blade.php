<div aria-hidden="true" aria-labelledby="createInventoryModalLabel" class="modal fade" id="createInventoryModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header">
				<div>
					<span class="loan-eyebrow">Catalogo de recursos</span>
					<h2 class="modal-title h4 mb-0 mt-1" id="createInventoryModalLabel">Agregar recurso</h2>
				</div>
				<button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
			</div>
			<form id="createInventoryForm">
				<div class="modal-body p-4">
					<div class="alert alert-danger d-none" id="inventoryFormError" role="alert"></div>
					<div class="mb-3">
						<label class="form-label" for="inventoryName">Nombre del recurso</label>
						<input class="form-control" id="inventoryName" maxlength="100" name="nombre" required type="text">
					</div>
					<div class="mb-3">
						<label class="form-label" for="inventoryQuantity">Cantidad</label>
						<input class="form-control" id="inventoryQuantity" min="0" name="cantidad" required type="number" value="0">
					</div>
					<div>
						<label class="form-label" for="inventoryDescription">Descripcion</label>
						<textarea class="form-control" id="inventoryDescription" name="descripcion" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
					<button class="btn btn-coral" id="saveInventoryButton" type="submit">Guardar recurso</button>
				</div>
			</form>
		</div>
	</div>
</div>
