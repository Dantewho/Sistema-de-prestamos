document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const userInfoModalElement = document.querySelector('#userInfoModal');
    const createLoanModalElement = document.querySelector('#createLoanModal');
    const createLoanForm = document.querySelector('#createLoanForm');
    const loanTypeInputs = document.querySelectorAll('input[name="tipo_solicitud"]');
    const aulaField = document.querySelector('#aula_id');
    const inventoryField = document.querySelector('#inventario_id');
    const quantityField = document.querySelector('#cantidad');
    const aulaFieldGroup = document.querySelector('#aulaFieldGroup');
    const inventoryFieldGroup = document.querySelector('#inventoryFieldGroup');
    const quantityFieldGroup = document.querySelector('#quantityFieldGroup');
    const startDateField = document.querySelector('#fecha_inicio');
    const deliveryDateField = document.querySelector('#fecha_entrega');
    const requesterField = document.querySelector('#usuario_solicitante_id');
    const inventoryAvailabilityText = document.querySelector('#inventoryAvailabilityText');
    const loanFormError = document.querySelector('#loanFormError');
    const requestsTableBody = document.querySelector('#requestsTableBody');
    const requestCount = document.querySelector('#requestCount');
    const requestStats = document.querySelectorAll('[data-request-stat]');

    const apiRequest = async (url, options = {}) => {
        const response = await fetch(url, {
            ...options,
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, ...options.headers },
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(Object.values(data.errors ?? {}).flat().join(' ') || data.message || 'No fue posible completar la solicitud.');
        return data;
    };
    const escapeHtml = (value) => String(value ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;');
    const showError = (message) => { loanFormError.textContent = message; loanFormError.classList.remove('d-none'); };
    const clearError = () => loanFormError?.classList.add('d-none');

    const userTriggers = document.querySelectorAll('#userPanelButton, #userPanelTrigger');
    if (userInfoModalElement && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(userInfoModalElement);
        userTriggers.forEach((trigger) => trigger.addEventListener('click', () => modal.show()));
    }

    let requests = [];
    const loadLoanOptions = async () => {
        const [users, classrooms, resources] = await Promise.all([apiRequest('/api/perfiles'), apiRequest('/api/aulas'), apiRequest('/api/inventario')]);
        requesterField.innerHTML = '<option value="">Selecciona un usuario</option>' + users.map((user) => `<option value="${user.id}">${escapeHtml(user.name)} (${escapeHtml(user.usuario)})</option>`).join('');
        aulaField.innerHTML = '<option value="">Selecciona un aula</option>' + classrooms.map((classroom) => `<option value="${classroom.id}">${escapeHtml(classroom.numero)} - ${escapeHtml(classroom.edificio?.nombre)}</option>`).join('');
        inventoryField.innerHTML = '<option value="">Selecciona un recurso</option>' + resources.filter((resource) => resource.cantidad > 0).map((resource) => `<option data-available="${resource.cantidad}" value="${resource.id}">${escapeHtml(resource.nombre)} (${resource.cantidad} disponibles)</option>`).join('');
    };

    const updateLoanTypeFields = () => {
        const isClassroomLoan = document.querySelector('input[name="tipo_solicitud"]:checked')?.value === 'aula';
        aulaFieldGroup.classList.toggle('d-none', !isClassroomLoan);
        inventoryFieldGroup.classList.toggle('d-none', isClassroomLoan);
        quantityFieldGroup.classList.toggle('d-none', isClassroomLoan);
        aulaField.required = isClassroomLoan;
        inventoryField.required = !isClassroomLoan;
        quantityField.required = !isClassroomLoan;
        if (isClassroomLoan) { inventoryField.value = ''; quantityField.value = 1; } else { aulaField.value = ''; }
    };
    const updateInventoryAvailability = () => {
        const available = Number(inventoryField.selectedOptions[0]?.dataset.available || 0);
        quantityField.max = available || 1;
        inventoryAvailabilityText.textContent = available ? `${available} disponibles` : '';
        if (available > 0 && Number(quantityField.value) > available) quantityField.value = available;
    };
    const renderRequests = () => {
        const query = document.querySelector('#nombre').value.toLowerCase().trim();
        const state = document.querySelector('#estado').value.toLowerCase();
        const type = document.querySelector('#tipo').value.toLowerCase();
        const filtered = requests.filter((request) => {
            const resourceName = request.tipo_solicitud === 'aula' ? request.aula?.numero : request.inventario?.nombre;
            const normalizedState = state === 'activos' ? 'activa' : state === 'pendientes' ? 'pendiente' : state;
            return `${resourceName || ''} ${request.solicitante?.name || ''}`.toLowerCase().includes(query) && (state === 'todos' || request.estado === normalizedState) && (type === 'todos' || request.tipo_solicitud === (type === 'aulas' ? 'aula' : 'inventario'));
        });
        requestCount.textContent = `${filtered.length} resultados`;
        requestsTableBody.innerHTML = filtered.length ? filtered.map((request) => `<tr><td>${escapeHtml(request.solicitante?.name)}</td><td>${escapeHtml(request.tipo_solicitud === 'aula' ? `Aula ${request.aula?.numero}` : request.inventario?.nombre)}</td><td>${escapeHtml(request.solicitante?.usuario)}</td><td>${escapeHtml(new Date(request.fecha_inicio).toLocaleDateString('es-MX'))}</td><td>${escapeHtml(request.identificacion || 'No especificada')}</td><td>${escapeHtml(request.prestador?.name || 'Pendiente')}</td><td><span class="badge ${request.estado === 'activa' ? 'bg-success' : 'loan-badge'}">${escapeHtml(request.estado)}</span></td></tr>`).join('') : '<tr><td class="loan-empty text-center" colspan="7">No hay solicitudes que coincidan.</td></tr>';
        requestStats.forEach((stat) => { stat.textContent = stat.dataset.requestStat === 'total' ? requests.length : requests.filter((request) => request.estado === stat.dataset.requestStat).length; });
    };
    const loadRequests = async () => { requests = await apiRequest('/api/solicitudes'); renderRequests(); };

    if (createLoanModalElement && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(createLoanModalElement);
        document.querySelectorAll('#createLoanButton, #createLoanEmptyButton').forEach((button) => button.addEventListener('click', async () => { clearError(); await loadLoanOptions(); updateLoanTypeFields(); modal.show(); }));
    }
    loanTypeInputs.forEach((input) => input.addEventListener('change', updateLoanTypeFields));
    inventoryField?.addEventListener('change', updateInventoryAvailability);
    quantityField?.addEventListener('input', updateInventoryAvailability);
    startDateField?.addEventListener('change', () => { deliveryDateField.min = startDateField.value; });
    deliveryDateField?.addEventListener('change', () => deliveryDateField.setCustomValidity(startDateField.value && deliveryDateField.value < startDateField.value ? 'La fecha de entrega debe ser igual o posterior a la fecha de inicio.' : ''));
    ['#nombre', '#estado', '#tipo'].forEach((selector) => document.querySelector(selector)?.addEventListener('input', renderRequests));
    document.querySelector('#clearLoanFilters')?.addEventListener('click', () => { document.querySelector('#nombre').value = ''; document.querySelector('#estado').value = 'Todos'; document.querySelector('#tipo').value = 'Todos'; renderRequests(); });

    createLoanForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearError();
        const isClassroomLoan = document.querySelector('input[name="tipo_solicitud"]:checked').value === 'aula';
        const available = Number(inventoryField.selectedOptions[0]?.dataset.available || 0);
        if (!isClassroomLoan && Number(quantityField.value) > available) { showError('La cantidad solicitada supera la existencia disponible.'); return; }
        try {
            await apiRequest('/api/solicitudes', { method: 'POST', body: JSON.stringify({ identificacion: document.querySelector('#identificacion').value, usuario_solicitante_id: Number(requesterField.value), tipo_solicitud: isClassroomLoan ? 'aula' : 'inventario', aula_id: isClassroomLoan ? Number(aulaField.value) : null, inventario_id: isClassroomLoan ? null : Number(inventoryField.value), cantidad: isClassroomLoan ? null : Number(quantityField.value), fecha_inicio: startDateField.value, fecha_fin: deliveryDateField.value || null, descripcion: document.querySelector('#descripcion').value || null }) });
            createLoanForm.reset(); updateLoanTypeFields(); bootstrap.Modal.getInstance(createLoanModalElement)?.hide(); await loadRequests();
        } catch (error) { showError(error.message); }
    });

    updateLoanTypeFields();
    loadRequests().catch(() => {});
});
