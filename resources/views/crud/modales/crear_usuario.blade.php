<div aria-hidden="true" aria-labelledby="createUserModalLabel" class="modal fade" id="createUserModal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content loan-modal">
			<div class="modal-header loan-modal-header"><h2 class="modal-title h4" id="createUserModalLabel">Crear usuario</h2><button aria-label="Cerrar" class="btn-close" data-bs-dismiss="modal" type="button"></button></div>
			<form id="createUserForm"><div class="modal-body p-4"><div class="alert alert-danger d-none" id="createUserFormError" role="alert"></div>
				<label class="form-label" for="newUserName">Nombre completo</label><input class="form-control mb-3" id="newUserName" required type="text">
				<label class="form-label" for="newUserUsername">Usuario</label><input class="form-control mb-3" id="newUserUsername" required type="text">
				<label class="form-label" for="newUserEmail">Correo electronico</label><input class="form-control mb-3" id="newUserEmail" required type="email">
				<label class="form-label" for="newUserType">Tipo de usuario</label><select class="form-select mb-3" id="newUserType"><option value="1">Administrador</option><option selected value="2">Usuario</option><option value="3">Profesor</option></select>
				<label class="form-label" for="newUserPassword">Contraseña</label><input class="form-control" id="newUserPassword" minlength="8" required type="password">
			</div><div class="modal-footer"><button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button><button class="btn btn-coral" type="submit">Crear usuario</button></div></form>
		</div>
	</div>
</div>
