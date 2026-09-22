<div aria-hidden="true" aria-labelledby="editUserModalLabel" class="modal fade" id="editUserModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header"><h2 class="modal-title h4" id="editUserModalLabel">Editar usuario</h2><button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
			<form id="editUserForm"><div class="modal-body p-4"><div class="alert alert-danger d-none" id="editUserFormError" role="alert"></div><input id="editUserId" type="hidden">
				<label class="form-label" for="editUserName">Nombre completo</label><input class="form-control mb-3" id="editUserName" required type="text">
				<label class="form-label" for="editUserUsername">Usuario</label><input class="form-control mb-3" id="editUserUsername" required type="text">
				<label class="form-label" for="editUserEmail">Correo electronico</label><input class="form-control mb-3" id="editUserEmail" required type="email">
				<label class="form-label" for="editUserType">Tipo de usuario</label><select class="form-select mb-3" id="editUserType"><option value="1">Administrador</option><option value="2">Usuario</option><option value="3">Profesor</option></select>
				<label class="form-label" for="editUserPassword">Nueva contraseña (opcional)</label><input class="form-control" id="editUserPassword" minlength="8" type="password">
			</div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button><button class="btn btn-coral" type="submit">Guardar cambios</button></div></form>
		</div>
	</div>
</div>
