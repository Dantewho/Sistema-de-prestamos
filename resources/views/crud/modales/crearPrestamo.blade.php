<div aria-hidden="true" aria-labelledby="createLoanModalLabel" class="modal fade" id="createLoanModal" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header">
				<div>
					<span class="loan-eyebrow">Nueva solicitud</span>
					<h2 class="modal-title h4 mb-0 mt-1" id="createLoanModalLabel">Registrar prestamo</h2>
				</div>
				<button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button>
			</div>

			<form id="createLoanForm">
				<div class="modal-body p-4">
					<div class="alert alert-info small" role="alert">
						Completa los datos de la solicitud. Los recursos muestran la cantidad disponible.
					</div>
					<div class="alert alert-danger d-none" id="loanFormError" role="alert"></div>

					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label" for="identificacion">Tipo de identificacion</label>
							<select class="form-select" id="identificacion" name="identificacion" required>
								<option selected disabled value="">Selecciona una opcion</option>
								<option value="INE">INE</option>
								<option value="Credencial escolar">Credencial escolar</option>
							</select>
						</div>

						<div class="col-md-6">
							<label class="form-label" for="usuario_solicitante_id">Usuario solicitante</label>
							<select class="form-select" id="usuario_solicitante_id" name="usuario_solicitante_id" required>
								<option value="">Cargando usuarios...</option>
							</select>
						</div>

						<div class="col-md-6">
							<label class="form-label" for="usuario_prestador">Usuario prestador</label>
							<input class="form-control" id="usuario_prestador" name="usuario_prestador" readonly type="text" value="{{ auth()->user()->name }}">
							<input name="usuario_prestador_id" type="hidden" value="{{ auth()->id() }}">
						</div>

						<div class="col-12">
							<fieldset>
								<legend class="form-label mb-2">Que deseas prestar?</legend>
								<div class="d-flex flex-wrap gap-3">
									<div class="form-check">
										<input checked class="form-check-input" id="loanTypeAula" name="tipo_solicitud" type="radio" value="aula">
										<label class="form-check-label" for="loanTypeAula">Un aula</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" id="loanTypeInventario" name="tipo_solicitud" type="radio" value="inventario">
										<label class="form-check-label" for="loanTypeInventario">Inventario</label>
									</div>
								</div>
							</fieldset>
						</div>

						<div class="col-md-6" id="aulaFieldGroup">
							<label class="form-label" for="aula_id">Aula</label>
							<select class="form-select" id="aula_id" name="aula_id">
								<option value="">Cargando aulas...</option>
							</select>
						</div>

						<div class="col-md-6" id="inventoryFieldGroup">
							<label class="form-label" for="inventario_id">Inventario</label>
							<select class="form-select" id="inventario_id" name="inventario_id">
								<option value="">Cargando recursos...</option>
							</select>
						</div>

						<div class="col-md-6 d-none" id="quantityFieldGroup">
							<label class="form-label" for="cantidad">Cantidad</label>
							<input class="form-control" id="cantidad" min="1" name="cantidad" type="number" value="1">
							<div class="form-text" id="inventoryAvailabilityText"></div>
						</div>

						<div class="col-md-6">
							<label class="form-label" for="fecha_inicio">Fecha de inicio</label>
							<input class="form-control" id="fecha_inicio" name="fecha_inicio" required type="datetime-local">
						</div>

						<div class="col-md-6">
							<label class="form-label" for="fecha_entrega">Fecha de entrega</label>
							<input class="form-control" id="fecha_entrega" name="fecha_fin" required type="datetime-local">
							<div class="form-text">Debe ser igual o posterior a la fecha de inicio.</div>
						</div>

						<div class="col-12">
							<label class="form-label" for="descripcion">Descripcion</label>
							<textarea class="form-control" id="descripcion" name="descripcion" placeholder="Agrega una nota opcional" rows="3"></textarea>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
					<button class="btn btn-coral" id="saveLoanButton" type="submit">Guardar solicitud</button>
				</div>
			</form>
		</div>
	</div>
</div>
