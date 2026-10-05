import { validateForm } from './validaciones.js';

document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const userInfoModalElement = document.querySelector('#userInfoModal');
    const createLoanModalElement = document.querySelector('#createLoanModal');
    const loanDetailsModalElement = document.querySelector('#loanDetailsModal');
    const loanDetailsContent = document.querySelector('#loanDetailsContent');
    const createLoanModalTitle = document.querySelector('#createLoanModalLabel');
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
    const identificationField = document.querySelector('#identificacion');
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

    if (!createLoanModalElement) return;

    let requests = [];
    let editingRequestId = null;
    const loadLoanOptions = async (editingRequest = null) => {
        const [users, classrooms, resources] = await Promise.all([apiRequest('/api/perfiles'), apiRequest('/api/aulas'), apiRequest('/api/inventario')]);
        requesterField.innerHTML = '<option value="">Selecciona un usuario</option>' + users.map((user) => `<option value="${user.id}">${escapeHtml(user.name)} (${escapeHtml(user.usuario)})</option>`).join('');
        aulaField.innerHTML = '<option value="">Selecciona un aula</option>' + classrooms.map((classroom) => `<option value="${classroom.id}">${escapeHtml(classroom.numero)} - ${escapeHtml(classroom.edificio?.nombre)}</option>`).join('');
        inventoryField.innerHTML = '<option value="">Selecciona un recurso</option>' + resources.map((resource) => {
            const available = Number(resource.cantidad) + (editingRequest?.tipo_solicitud === 'inventario' && Number(editingRequest.inventario_id) === Number(resource.id) ? Number(editingRequest.cantidad) : 0);
            return available > 0 ? `<option data-available="${available}" value="${resource.id}">${escapeHtml(resource.nombre)} (${available} disponibles)</option>` : '';
        }).join('');
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
    const formatDateTime = (value) => value ? new Date(value).toLocaleString('es-MX') : 'No especificada';
    const toDateTimeLocal = (value) => {
        if (!value) return '';
        const date = new Date(value);
        date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
        return date.toISOString().slice(0, 16);
    };
    const actionButton = (request, action, label, icon, classes, disabled = false) => `<button aria-label="${label}" class="btn btn-sm ${classes}" data-id="${request.id}" data-request-action="${action}" title="${label}" type="button"${disabled ? ' disabled' : ''}>${icon}</button>`;
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
        requestsTableBody.innerHTML = filtered.length ? filtered.map((request) => {
            const isTerminal = ['finalizada', 'cancelada'].includes(request.estado);
            const eyeIcon = '<svg aria-hidden="true" height="16" viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
            const pencilIcon = '<svg aria-hidden="true" height="16" viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>';
            const checkIcon = '<svg aria-hidden="true" height="16" viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>';
            const downloadIcon = '<svg aria-hidden="true" height="16" viewBox="0 0 24 24" width="16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>';
            const resourceName = request.tipo_solicitud === 'aula' ? `Aula ${request.aula?.numero}` : request.inventario?.nombre;
            return `<tr><td>${escapeHtml(request.solicitante?.name)}</td><td>${escapeHtml(resourceName)}</td><td>${escapeHtml(request.solicitante?.usuario)}</td><td>${escapeHtml(new Date(request.fecha_inicio).toLocaleDateString('es-MX'))}</td><td>${escapeHtml(request.identificacion || 'No especificada')}</td><td>${escapeHtml(request.prestador?.name || 'Pendiente')}</td><td><span class="badge ${request.estado === 'activa' ? 'bg-success' : 'loan-badge'}">${escapeHtml(request.estado)}</span></td><td><div class="d-flex gap-1">${actionButton(request, 'view', 'Ver detalles', eyeIcon, 'btn-outline-primary')}<a aria-label="Descargar PDF" class="btn btn-sm btn-outline-danger" href="/solicitudes/${encodeURIComponent(request.id)}/pdf" title="Descargar PDF">${downloadIcon}</a>${actionButton(request, 'edit', 'Editar prestamo', pencilIcon, 'btn-outline-secondary', isTerminal)}${actionButton(request, 'finalize', 'Finalizar prestamo', checkIcon, 'btn-outline-success', isTerminal)}</div></td></tr>`;
        }).join('') : '<tr><td class="loan-empty text-center" colspan="8">No hay solicitudes que coincidan.</td></tr>';
        requestStats.forEach((stat) => { stat.textContent = stat.dataset.requestStat === 'total' ? requests.length : requests.filter((request) => request.estado === stat.dataset.requestStat).length; });
    };
    const loadRequests = async () => { requests = await apiRequest('/api/solicitudes'); renderRequests(); };

    const showLoanDetails = (request) => {
        const product = request.tipo_solicitud === 'aula'
            ? `Aula ${request.aula?.numero || ''} - ${request.aula?.edificio?.nombre || ''}`
            : request.inventario?.nombre || 'Recurso no disponible';
        const details = [
            ['Solicitante', request.solicitante?.name],
            ['Producto o aula', product],
            ['Cantidad', request.tipo_solicitud === 'inventario' ? request.cantidad : 'No aplica'],
            ['Prestado por', request.prestador?.name || 'Pendiente'],
            ['Identificacion', request.identificacion || 'No especificada'],
            ['Inicio', formatDateTime(request.fecha_inicio)],
            ['Entrega', formatDateTime(request.fecha_fin)],
            ['Estado', request.estado],
            ['Descripcion', request.descripcion || 'Sin descripcion'],
        ];
        loanDetailsContent.innerHTML = `<dl class="row mb-0">${details.map(([label, value]) => `<dt class="col-sm-4 text-muted">${escapeHtml(label)}</dt><dd class="col-sm-8">${escapeHtml(value)}</dd>`).join('')}</dl>`;
        bootstrap.Modal.getOrCreateInstance(loanDetailsModalElement).show();
    };
    const openEditLoan = async (request) => {
        editingRequestId = request.id;
        clearError();
        try {
            await loadLoanOptions(request);
            document.querySelector(`#loanType${request.tipo_solicitud === 'aula' ? 'Aula' : 'Inventario'}`).checked = true;
            requesterField.value = request.usuario_solicitante_id;
            requesterField.disabled = true;
            aulaField.value = request.aula_id || '';
            inventoryField.value = request.inventario_id || '';
            quantityField.value = request.cantidad || 1;
            identificationField.value = request.identificacion || '';
            identificationField.required = false;
            startDateField.value = toDateTimeLocal(request.fecha_inicio);
            deliveryDateField.value = toDateTimeLocal(request.fecha_fin);
            deliveryDateField.min = startDateField.value;
            document.querySelector('#descripcion').value = request.descripcion || '';
            updateLoanTypeFields();
            updateInventoryAvailability();
            createLoanModalTitle.textContent = 'Editar prestamo';
            document.querySelector('#saveLoanButton').textContent = 'Guardar cambios';
            bootstrap.Modal.getOrCreateInstance(createLoanModalElement).show();
        } catch (error) {
            editingRequestId = null;
            showError(error.message);
        }
    };

    if (createLoanModalElement && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(createLoanModalElement);
        document.querySelectorAll('#createLoanButton, #createLoanEmptyButton').forEach((button) => button.addEventListener('click', async () => {
            editingRequestId = null;
            createLoanForm.reset();
            requesterField.disabled = false;
            identificationField.required = true;
            createLoanModalTitle.textContent = 'Registrar prestamo';
            document.querySelector('#saveLoanButton').textContent = 'Guardar solicitud';
            clearError();
            try {
                await loadLoanOptions();
                updateLoanTypeFields();
                modal.show();
            } catch (error) {
                showError(error.message);
                modal.show();
            }
        }));
    }
    createLoanModalElement?.addEventListener('hidden.bs.modal', () => {
        editingRequestId = null;
        requesterField.disabled = false;
        identificationField.required = true;
    });
    requestsTableBody?.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-request-action]');
        if (!button) return;
        const request = requests.find((item) => String(item.id) === button.dataset.id);
        if (!request) return;

        if (button.dataset.requestAction === 'view') {
            showLoanDetails(request);
            return;
        }
        if (button.dataset.requestAction === 'edit') {
            await openEditLoan(request);
            return;
        }
        if (button.dataset.requestAction === 'finalize' && window.confirm('¿Deseas marcar este prestamo como finalizado?')) {
            button.disabled = true;
            try {
                await apiRequest(`/api/solicitudes/${request.id}/finalizar`, { method: 'POST' });
                await loadRequests();
            } catch (error) {
                window.alert(error.message);
                button.disabled = false;
            }
        }
    });
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
        if (!validateForm(event.currentTarget)) return;
        const isClassroomLoan = document.querySelector('input[name="tipo_solicitud"]:checked').value === 'aula';
        const available = Number(inventoryField.selectedOptions[0]?.dataset.available || 0);
        if (!isClassroomLoan && Number(quantityField.value) > available) { showError('La cantidad solicitada supera la existencia disponible.'); return; }
        try {
            const payload = { identificacion: identificationField.value || null, tipo_solicitud: isClassroomLoan ? 'aula' : 'inventario', aula_id: isClassroomLoan ? Number(aulaField.value) : null, inventario_id: isClassroomLoan ? null : Number(inventoryField.value), cantidad: isClassroomLoan ? null : Number(quantityField.value), fecha_inicio: startDateField.value, fecha_fin: deliveryDateField.value || null, descripcion: document.querySelector('#descripcion').value || null };
            if (!editingRequestId) payload.usuario_solicitante_id = Number(requesterField.value);
            await apiRequest(editingRequestId ? `/api/solicitudes/${editingRequestId}` : '/api/solicitudes', { method: editingRequestId ? 'PATCH' : 'POST', body: JSON.stringify(payload) });
            editingRequestId = null;
            requesterField.disabled = false;
            identificationField.required = true;
            createLoanForm.reset();
            createLoanModalTitle.textContent = 'Registrar prestamo';
            document.querySelector('#saveLoanButton').textContent = 'Guardar solicitud';
            updateLoanTypeFields();
            bootstrap.Modal.getInstance(createLoanModalElement)?.hide();
            await loadRequests();
        } catch (error) { showError(error.message); }
    });

    updateLoanTypeFields();
    loadRequests().catch(() => {});
});
