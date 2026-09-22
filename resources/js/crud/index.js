document.addEventListener('DOMContentLoaded', () => {
	const userPanelTriggers = document.querySelectorAll('#userPanelButton, #userPanelTrigger');
	const userInfoModalElement = document.querySelector('#userInfoModal');
	const createLoanButtons = document.querySelectorAll('#createLoanButton, #createLoanEmptyButton');
	const createLoanModalElement = document.querySelector('#createLoanModal');
	const loanTypeInputs = document.querySelectorAll('input[name="tipo_solicitud"]');
	const aulaField = document.querySelector('#aula_id');
	const inventoryField = document.querySelector('#inventario_id');
	const aulaFieldGroup = document.querySelector('#aulaFieldGroup');
	const inventoryFieldGroup = document.querySelector('#inventoryFieldGroup');
	const startDateField = document.querySelector('#fecha_inicio');
	const deliveryDateField = document.querySelector('#fecha_entrega');

	if (userPanelTriggers.length && userInfoModalElement && window.bootstrap) {
		const userInfoModal = bootstrap.Modal.getOrCreateInstance(userInfoModalElement);

		userPanelTriggers.forEach((userPanelTrigger) => {
			userPanelTrigger.addEventListener('click', () => userInfoModal.show());
			userPanelTrigger.addEventListener('keydown', (event) => {
				if (event.key === 'Enter' || event.key === ' ') {
					event.preventDefault();
					userInfoModal.show();
				}
			});
		});
	}

	if (createLoanButtons.length && createLoanModalElement && window.bootstrap) {
		const createLoanModal = bootstrap.Modal.getOrCreateInstance(createLoanModalElement);
		createLoanButtons.forEach((createLoanButton) => {
			createLoanButton.addEventListener('click', () => createLoanModal.show());
		});
	}

	const updateLoanTypeFields = () => {
		const selectedType = document.querySelector('input[name="tipo_solicitud"]:checked')?.value;
		const isClassroomLoan = selectedType === 'aula';

		if (!aulaField || !inventoryField || !aulaFieldGroup || !inventoryFieldGroup) {
			return;
		}

		aulaField.disabled = !isClassroomLoan;
		inventoryField.disabled = isClassroomLoan;
		aulaField.value = isClassroomLoan ? aulaField.value : '0';
		inventoryField.value = isClassroomLoan ? '0' : inventoryField.value;
		aulaFieldGroup.classList.toggle('d-none', !isClassroomLoan);
		inventoryFieldGroup.classList.toggle('d-none', isClassroomLoan);
	};

	loanTypeInputs.forEach((loanTypeInput) => {
		loanTypeInput.addEventListener('change', updateLoanTypeFields);
	});

	if (startDateField && deliveryDateField) {
		startDateField.addEventListener('change', () => {
			deliveryDateField.min = startDateField.value;

			if (deliveryDateField.value && deliveryDateField.value < startDateField.value) {
				deliveryDateField.value = '';
			}
		});

		deliveryDateField.addEventListener('change', () => {
			if (startDateField.value && deliveryDateField.value < startDateField.value) {
				deliveryDateField.setCustomValidity('La fecha de entrega debe ser igual o posterior a la fecha de inicio.');
				return;
			}

			deliveryDateField.setCustomValidity('');
		});
	}

	updateLoanTypeFields();
});
