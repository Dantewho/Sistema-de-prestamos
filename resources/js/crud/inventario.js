document.addEventListener('DOMContentLoaded', () => {
	const aulasButton = document.querySelector('#inventoryModeAulas');
	const resourcesButton = document.querySelector('#inventoryModeResources');
	const createInventoryItemButton = document.querySelector('#createInventoryItemButton');
	const createClassroomModalElement = document.querySelector('#createClassroomModal');
	const createInventoryModalElement = document.querySelector('#createInventoryModal');
	const classroomForm = document.querySelector('#createClassroomForm');
	const inventoryForm = document.querySelector('#createInventoryForm');
	const buildingSelect = document.querySelector('#classroomBuilding');
	const newBuildingFields = document.querySelector('#newBuildingFields');
	const aulasTableHead = document.querySelector('#classroomsTableHead');
	const aulasTableBody = document.querySelector('#classroomsTableBody');
	const resourcesTableHead = document.querySelector('#resourcesTableHead');
	const resourcesTableBody = document.querySelector('#resourcesTableBody');
	const title = document.querySelector('#inventory-title');
	const filtersTitle = document.querySelector('#inventory-filters-title');
	const searchLabel = document.querySelector('label[for="inventorySearch"]');
	const searchInput = document.querySelector('#inventorySearch');
	const availabilityFilter = document.querySelector('#inventoryAvailability');
	const buildingFilter = document.querySelector('#buildingFilter');
	const editBuildingButton = document.querySelector('#editBuildingButton');
	const count = document.querySelector('#inventoryCount');
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

	if (!aulasButton || !resourcesButton || !createInventoryItemButton) {
		return;
	}

	const createClassroomModal = createClassroomModalElement && window.bootstrap
		? bootstrap.Modal.getOrCreateInstance(createClassroomModalElement)
		: null;
	const createInventoryModal = createInventoryModalElement && window.bootstrap
		? bootstrap.Modal.getOrCreateInstance(createInventoryModalElement)
		: null;
	const createBuildingModal = document.querySelector('#createBuildingModal') && window.bootstrap
		? bootstrap.Modal.getOrCreateInstance(document.querySelector('#createBuildingModal'))
		: null;
	const editCatalogModal = document.querySelector('#editCatalogModal') && window.bootstrap
		? bootstrap.Modal.getOrCreateInstance(document.querySelector('#editCatalogModal'))
		: null;

	const apiRequest = async (url, options = {}) => {
		const response = await fetch(url, {
			...options,
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': csrfToken,
				...options.headers,
			},
		});

		const data = await response.json().catch(() => ({}));

		if (!response.ok) {
			throw new Error(Object.values(data.errors ?? {}).flat().join(' ') || data.message || 'No fue posible completar la solicitud.');
		}

		return data;
	};

	const escapeHtml = (value) => String(value ?? '')
		.replaceAll('&', '&amp;')
		.replaceAll('<', '&lt;')
		.replaceAll('>', '&gt;')
		.replaceAll('"', '&quot;')
		.replaceAll("'", '&#039;');

	const showFormError = (elementId, error) => {
		const errorElement = document.querySelector(`#${elementId}`);

		if (!errorElement) {
			return;
		}

		errorElement.textContent = error.message;
		errorElement.classList.remove('d-none');
	};

	const clearFormError = (elementId) => {
		document.querySelector(`#${elementId}`)?.classList.add('d-none');
	};
	let classrooms = [];
	let resources = [];
	let buildings = [];

	const loadBuildings = async () => {
		if (!buildingSelect) {
			return;
		}

		buildingSelect.innerHTML = '<option value="">Cargando edificios...</option><option value="new">Crear nuevo edificio</option>';

		try {
			buildings = await apiRequest('/api/edificios');
			buildingSelect.innerHTML = '<option value="">Selecciona un edificio</option><option value="new">Crear nuevo edificio</option>';
			buildings.forEach((building) => {
				buildingSelect.insertAdjacentHTML('beforeend', `<option value="${building.id}">${escapeHtml(building.nombre)}</option>`);
			});
			if (buildingFilter) {
				buildingFilter.innerHTML = '<option value="">Todos los edificios</option>' + buildings.map((building) => `<option value="${building.id}">${escapeHtml(building.nombre)}</option>`).join('');
			}
		} catch (error) {
			buildingSelect.innerHTML = '<option value="">No se pudieron cargar los edificios</option><option value="new">Crear nuevo edificio</option>';
			showFormError('classroomFormError', error);
		}
	};

	const renderCatalog = () => {
		const search = searchInput.value.toLowerCase().trim();
		const availability = availabilityFilter?.value || 'Todos';
		const selectedBuilding = buildingFilter?.value || '';
		const filteredClassrooms = classrooms.filter((classroom) => `${classroom.numero} ${classroom.descripcion || ''} ${classroom.edificio?.nombre || ''}`.toLowerCase().includes(search) && (!selectedBuilding || String(classroom.edificio_id) === selectedBuilding));
		const filteredResources = resources.filter((resource) => `${resource.nombre} ${resource.descripcion || ''}`.toLowerCase().includes(search) && (availability === 'Todos' || (availability === 'Disponible' && resource.cantidad > 0) || (availability === 'No disponible' && resource.cantidad === 0)));
		const classroomRows = filteredClassrooms.length
			? filteredClassrooms.map((classroom) => `<tr><td>${escapeHtml(classroom.numero)}</td><td>${escapeHtml(classroom.edificio?.nombre)}</td><td>${escapeHtml(classroom.descripcion || 'Sin descripcion')}</td><td><button class="btn btn-sm btn-outline-primary edit-classroom" data-id="${classroom.id}" type="button">Editar</button> <button class="btn btn-sm btn-outline-danger delete-classroom" data-id="${classroom.id}" type="button">Eliminar</button></td></tr>`).join('')
			: '<tr><td class="loan-empty text-center" colspan="4">No hay aulas que coincidan.</td></tr>';
		const resourceRows = filteredResources.length
			? filteredResources.map((resource) => `<tr><td>${escapeHtml(resource.nombre)}</td><td>${escapeHtml(resource.cantidad)}</td><td>${escapeHtml(resource.descripcion || 'Sin descripcion')}</td><td><button class="btn btn-sm btn-outline-primary edit-resource" data-id="${resource.id}" type="button">Editar</button> <button class="btn btn-sm btn-outline-danger delete-resource" data-id="${resource.id}" type="button">Eliminar</button></td></tr>`).join('')
			: '<tr><td class="loan-empty text-center" colspan="4">No hay recursos que coincidan.</td></tr>';
		aulasTableBody.innerHTML = classroomRows;
		resourcesTableBody.innerHTML = resourceRows;
		count.textContent = document.querySelector('#inventoryModeAulas.active') ? `${filteredClassrooms.length} aulas` : `${filteredResources.length} recursos`;
	};

	const loadCatalog = async () => {
		try {
			[classrooms, resources] = await Promise.all([
				apiRequest('/api/aulas'),
				apiRequest('/api/inventario'),
			]);
			renderCatalog();
		} catch (error) {
			aulasTableBody.innerHTML = `<tr><td class="loan-empty text-center" colspan="4">${escapeHtml(error.message)}</td></tr>`;
			resourcesTableBody.innerHTML = `<tr><td class="loan-empty text-center" colspan="4">${escapeHtml(error.message)}</td></tr>`;
		}
	};

	const showMode = (mode) => {
		const isAulas = mode === 'aulas';

		aulasButton.classList.toggle('active', isAulas);
		resourcesButton.classList.toggle('active', !isAulas);
		aulasTableHead.classList.toggle('d-none', !isAulas);
		aulasTableBody.classList.toggle('d-none', !isAulas);
		resourcesTableHead.classList.toggle('d-none', isAulas);
		resourcesTableBody.classList.toggle('d-none', isAulas);
		title.textContent = isAulas ? 'Aulas registradas' : 'Recursos registrados';
		filtersTitle.textContent = isAulas ? 'Filtrar aulas' : 'Filtrar recursos';
		searchLabel.textContent = isAulas ? 'Buscar aula' : 'Buscar recurso';
		searchInput.placeholder = isAulas ? 'Numero o descripcion del aula' : 'Nombre del equipo';
		document.querySelector('#buildingFilterGroup')?.classList.toggle('d-none', !isAulas);
		renderCatalog();
	};

	aulasButton.addEventListener('click', () => {
		showMode('aulas');
	});
	resourcesButton.addEventListener('click', () => {
		showMode('recursos');
	});
	[searchInput, availabilityFilter, buildingFilter].forEach((field) => field?.addEventListener('input', renderCatalog));
	editBuildingButton?.addEventListener('click', () => {
		const building = buildings.find((item) => String(item.id) === buildingFilter.value);
		if (!building) {
			window.alert('Selecciona un edificio para editar.');
			return;
		}
		document.querySelector('#editCatalogType').value = 'edificio';
		document.querySelector('#editCatalogId').value = building.id;
		document.querySelector('#editBuildingFieldsGroup').classList.remove('d-none');
		document.querySelector('#editClassroomBuildingGroup').classList.add('d-none');
		document.querySelector('#editClassroomNumberGroup').classList.add('d-none');
		document.querySelector('#editInventoryNameGroup').classList.add('d-none');
		document.querySelector('#editInventoryQuantityGroup').classList.add('d-none');
		document.querySelector('#editBuildingName').value = building.nombre;
		document.querySelector('#editBuildingDescription').value = building.descripcion || '';
		clearFormError('editCatalogFormError');
		editCatalogModal?.show();
	});
	document.querySelector('#openBuildingModalButton')?.addEventListener('click', () => {
		clearFormError('buildingFormError');
		createBuildingModal?.show();
	});

	document.querySelector('#createBuildingForm')?.addEventListener('submit', async (event) => {
		event.preventDefault();
		clearFormError('buildingFormError');
		try {
			await apiRequest('/api/edificios', { method: 'POST', body: JSON.stringify({ nombre: document.querySelector('#buildingName').value, descripcion: document.querySelector('#buildingDescription').value || null }) });
			event.target.reset();
			createBuildingModal?.hide();
			await loadBuildings();
		} catch (error) {
			showFormError('buildingFormError', error);
		}
	});
	createInventoryItemButton.addEventListener('click', () => {
		if (document.querySelector('#inventoryModeAulas.active')) {
			clearFormError('classroomFormError');
			loadBuildings();
			createClassroomModal?.show();
			return;
		}

		clearFormError('inventoryFormError');
		createInventoryModal?.show();
	});

	buildingSelect?.addEventListener('change', () => {
		newBuildingFields?.classList.toggle('d-none', buildingSelect.value !== 'new');
	});

	classroomForm?.addEventListener('submit', async (event) => {
		event.preventDefault();
		clearFormError('classroomFormError');

		try {
			let buildingId = buildingSelect.value;
			if (buildingId === 'new') {
				const building = await apiRequest('/api/edificios', {
					method: 'POST',
					body: JSON.stringify({
						nombre: document.querySelector('#newBuildingName').value,
						descripcion: document.querySelector('#newBuildingDescription').value || null,
					}),
				});
				buildingId = building.id;
			}

			await apiRequest('/api/aulas', {
				method: 'POST',
				body: JSON.stringify({
					edificio_id: Number(buildingId),
					numero: document.querySelector('#classroomNumber').value,
					descripcion: document.querySelector('#classroomDescription').value || null,
				}),
			});

			classroomForm.reset();
			newBuildingFields?.classList.add('d-none');
			createClassroomModal?.hide();
			await loadCatalog();
		} catch (error) {
			showFormError('classroomFormError', error);
		}
	});

	inventoryForm?.addEventListener('submit', async (event) => {
		event.preventDefault();
		clearFormError('inventoryFormError');

		try {
			await apiRequest('/api/inventario', {
				method: 'POST',
				body: JSON.stringify({
					nombre: document.querySelector('#inventoryName').value,
					cantidad: Number(document.querySelector('#inventoryQuantity').value),
					descripcion: document.querySelector('#inventoryDescription').value || null,
				}),
			});

			inventoryForm.reset();
			createInventoryModal?.hide();
			await loadCatalog();
		} catch (error) {
			showFormError('inventoryFormError', error);
		}
	});

	document.querySelector('#editCatalogForm')?.addEventListener('submit', async (event) => {
		event.preventDefault();
		clearFormError('editCatalogFormError');
		const type = document.querySelector('#editCatalogType').value;
		const id = document.querySelector('#editCatalogId').value;
		const payload = type === 'aula'
			? { edificio_id: Number(document.querySelector('#editClassroomBuilding').value), numero: document.querySelector('#editClassroomNumber').value, descripcion: document.querySelector('#editCatalogDescription').value || null }
			: type === 'recurso'
				? { nombre: document.querySelector('#editInventoryName').value, cantidad: Number(document.querySelector('#editInventoryQuantity').value), descripcion: document.querySelector('#editCatalogDescription').value || null }
				: { nombre: document.querySelector('#editBuildingName').value, descripcion: document.querySelector('#editBuildingDescription').value || null };
		try {
			await apiRequest(`/api/${type === 'aula' ? 'aulas' : type === 'recurso' ? 'inventario' : 'edificios'}/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
			editCatalogModal?.hide();
			await loadCatalog();
		} catch (error) {
			showFormError('editCatalogFormError', error);
		}
	});

	document.addEventListener('click', async (event) => {
		const editClassroomButton = event.target.closest('.edit-classroom');
		const editResourceButton = event.target.closest('.edit-resource');
		if (editClassroomButton || editResourceButton) {
			const isClassroom = Boolean(editClassroomButton);
			const item = (isClassroom ? classrooms : resources).find((record) => record.id === Number((editClassroomButton || editResourceButton).dataset.id));
			document.querySelector('#editCatalogType').value = isClassroom ? 'aula' : 'recurso';
			document.querySelector('#editCatalogId').value = item.id;
			document.querySelector('#editBuildingFieldsGroup').classList.add('d-none');
			document.querySelector('#editClassroomBuildingGroup').classList.toggle('d-none', !isClassroom);
			document.querySelector('#editClassroomNumberGroup').classList.toggle('d-none', !isClassroom);
			document.querySelector('#editInventoryNameGroup').classList.toggle('d-none', isClassroom);
			document.querySelector('#editInventoryQuantityGroup').classList.toggle('d-none', isClassroom);
			if (isClassroom) {
				document.querySelector('#editClassroomBuilding').value = String(item.edificio_id);
				document.querySelector('#editClassroomNumber').value = item.numero;
			} else {
				document.querySelector('#editInventoryName').value = item.nombre;
				document.querySelector('#editInventoryQuantity').value = item.cantidad;
			}
			document.querySelector('#editCatalogDescription').value = item.descripcion || '';
			clearFormError('editCatalogFormError');
			editCatalogModal?.show();
			return;
		}
		const classroomDeleteButton = event.target.closest('.delete-classroom');
		const resourceDeleteButton = event.target.closest('.delete-resource');
		const button = classroomDeleteButton || resourceDeleteButton;

		if (!button || !window.confirm('¿Deseas eliminar este registro?')) {
			return;
		}

		try {
			await apiRequest(`/api/${classroomDeleteButton ? 'aulas' : 'inventario'}/${button.dataset.id}`, { method: 'DELETE' });
			await loadCatalog();
		} catch (error) {
			window.alert(error.message);
		}
	});

	showMode('aulas');
	loadCatalog();
});
