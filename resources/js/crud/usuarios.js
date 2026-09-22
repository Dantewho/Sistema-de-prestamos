document.addEventListener('DOMContentLoaded', () => {
	const tableBody = document.querySelector('#usersTableBody');
	const search = document.querySelector('#userSearch');
	const typeFilter = document.querySelector('#userType');
	const count = document.querySelector('#userCount');
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
	const createModal = window.bootstrap && document.querySelector('#createUserModal') ? bootstrap.Modal.getOrCreateInstance(document.querySelector('#createUserModal')) : null;
	const editModal = window.bootstrap && document.querySelector('#editUserModal') ? bootstrap.Modal.getOrCreateInstance(document.querySelector('#editUserModal')) : null;
	let users = [];

	if (!tableBody) return;

	const apiRequest = async (url, options = {}) => {
		const response = await fetch(url, { ...options, headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, ...options.headers } });
		const data = await response.json().catch(() => ({}));
		if (!response.ok) throw new Error(Object.values(data.errors ?? {}).flat().join(' ') || data.message || 'No fue posible completar la solicitud.');
		return data;
	};
	const escapeHtml = (value) => String(value ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
	const userType = (value) => ({ 1: 'Administrador', 3: 'Profesor' })[value] || 'Usuario';
	const showError = (id, error) => { const element = document.querySelector(`#${id}`); if (element) { element.textContent = error.message; element.classList.remove('d-none'); } };
	const clearError = (id) => document.querySelector(`#${id}`)?.classList.add('d-none');

	const render = () => {
		const query = search.value.toLowerCase().trim();
		const selectedType = typeFilter.value;
		const filtered = users.filter((user) => `${user.name} ${user.usuario} ${user.email}`.toLowerCase().includes(query) && (selectedType === 'Todos' || userType(user.tipo_usuario) === selectedType));
		count.textContent = `${filtered.length} usuarios`;
		tableBody.innerHTML = filtered.length ? filtered.map((user) => `<tr><td>${user.id}</td><td>${escapeHtml(user.usuario)}</td><td>${escapeHtml(user.email)}</td><td>${userType(user.tipo_usuario)}</td><td>${user.solicitudes_realizadas_count ?? 0}</td><td><button class="btn btn-sm btn-outline-primary edit-user" data-id="${user.id}" type="button">Editar</button> <button class="btn btn-sm btn-outline-danger delete-user" data-id="${user.id}" type="button">Eliminar</button></td></tr>`).join('') : '<tr><td class="loan-empty text-center" colspan="6">No hay usuarios que coincidan.</td></tr>';
	};

	const loadUsers = async () => { users = await apiRequest('/api/perfiles'); render(); };
	search.addEventListener('input', render); typeFilter.addEventListener('change', render);
	document.querySelector('#clearUserFilters')?.addEventListener('click', () => { search.value = ''; typeFilter.value = 'Todos'; render(); });
	document.querySelector('#createUserButton')?.addEventListener('click', () => { clearError('createUserFormError'); createModal?.show(); });
	document.querySelector('#createUserForm')?.addEventListener('submit', async (event) => {
		event.preventDefault(); clearError('createUserFormError');
		try { await apiRequest('/api/perfiles', { method: 'POST', body: JSON.stringify({ name: document.querySelector('#newUserName').value, usuario: document.querySelector('#newUserUsername').value, email: document.querySelector('#newUserEmail').value, tipo_usuario: Number(document.querySelector('#newUserType').value), password: document.querySelector('#newUserPassword').value }) }); event.target.reset(); createModal?.hide(); await loadUsers(); } catch (error) { showError('createUserFormError', error); }
	});
	document.querySelector('#editUserForm')?.addEventListener('submit', async (event) => {
		event.preventDefault(); clearError('editUserFormError'); const password = document.querySelector('#editUserPassword').value;
		const payload = { name: document.querySelector('#editUserName').value, usuario: document.querySelector('#editUserUsername').value, email: document.querySelector('#editUserEmail').value, tipo_usuario: Number(document.querySelector('#editUserType').value) }; if (password) payload.password = password;
		try { await apiRequest(`/api/perfiles/${document.querySelector('#editUserId').value}`, { method: 'PUT', body: JSON.stringify(payload) }); editModal?.hide(); await loadUsers(); } catch (error) { showError('editUserFormError', error); }
	});
	document.addEventListener('click', async (event) => {
		const editButton = event.target.closest('.edit-user'); const deleteButton = event.target.closest('.delete-user');
		if (editButton) { const user = users.find((item) => item.id === Number(editButton.dataset.id)); document.querySelector('#editUserId').value = user.id; document.querySelector('#editUserName').value = user.name; document.querySelector('#editUserUsername').value = user.usuario; document.querySelector('#editUserEmail').value = user.email; document.querySelector('#editUserType').value = user.tipo_usuario; document.querySelector('#editUserPassword').value = ''; clearError('editUserFormError'); editModal?.show(); return; }
		if (deleteButton && window.confirm('¿Deseas eliminar este usuario?')) { try { await apiRequest(`/api/perfiles/${deleteButton.dataset.id}`, { method: 'DELETE' }); await loadUsers(); } catch (error) { window.alert(error.message); } }
	});
	loadUsers().catch((error) => { tableBody.innerHTML = `<tr><td class="loan-empty text-center" colspan="6">${escapeHtml(error.message)}</td></tr>`; });
});
