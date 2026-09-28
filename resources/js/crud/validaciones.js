const TEXT_PATTERNS = {
	person: /^[\p{L}\p{M} .'-]+$/u,
	username: /^[\p{L}\p{M}\d._-]+$/u,
	catalog: /^[\p{L}\p{M}\d .,#()'\-/]+$/u,
	room: /^[\p{L}\p{M}\d-]+$/u,
	description: /^[^<>]*$/u,
};

const FIELD_RULES = {
	newUserName: { label: 'Nombre completo', maxLength: 255, pattern: TEXT_PATTERNS.person, patternMessage: 'Solo se permiten letras, espacios, apóstrofes y guiones.' },
	editUserName: { label: 'Nombre completo', maxLength: 255, pattern: TEXT_PATTERNS.person, patternMessage: 'Solo se permiten letras, espacios, apóstrofes y guiones.' },
	newUserUsername: { label: 'Usuario', maxLength: 255, pattern: TEXT_PATTERNS.username, patternMessage: 'Usa letras, números, puntos, guiones o guiones bajos.' },
	editUserUsername: { label: 'Usuario', maxLength: 255, pattern: TEXT_PATTERNS.username, patternMessage: 'Usa letras, números, puntos, guiones o guiones bajos.' },
	newUserEmail: { label: 'Correo electrónico', maxLength: 255 },
	editUserEmail: { label: 'Correo electrónico', maxLength: 255 },
	newUserPassword: { label: 'Contraseña', minLength: 8, maxLength: 128 },
	editUserPassword: { label: 'Contraseña', minLength: 8, maxLength: 128, optional: true },
	classroomNumber: { label: 'Número de aula', maxLength: 10, pattern: TEXT_PATTERNS.room, patternMessage: 'Usa letras, números y guiones.' },
	editClassroomNumber: { label: 'Número de aula', maxLength: 10, pattern: TEXT_PATTERNS.room, patternMessage: 'Usa letras, números y guiones.' },
	newBuildingName: { label: 'Nombre del edificio', maxLength: 10, pattern: TEXT_PATTERNS.catalog, patternMessage: 'El nombre contiene caracteres no permitidos.' },
	buildingName: { label: 'Nombre del edificio', maxLength: 10, pattern: TEXT_PATTERNS.catalog, patternMessage: 'El nombre contiene caracteres no permitidos.' },
	editBuildingName: { label: 'Nombre del edificio', maxLength: 10, pattern: TEXT_PATTERNS.catalog, patternMessage: 'El nombre contiene caracteres no permitidos.' },
	inventoryName: { label: 'Nombre del recurso', maxLength: 100, pattern: TEXT_PATTERNS.catalog, patternMessage: 'El nombre contiene caracteres no permitidos.' },
	editInventoryName: { label: 'Nombre del recurso', maxLength: 100, pattern: TEXT_PATTERNS.catalog, patternMessage: 'El nombre contiene caracteres no permitidos.' },
	newBuildingDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	buildingDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	editBuildingDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	classroomDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	editCatalogDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	inventoryDescription: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	descripcion: { label: 'Descripción', maxLength: 1000, pattern: TEXT_PATTERNS.description, patternMessage: 'No se permiten etiquetas HTML.' },
	cantidad: { label: 'Cantidad', integer: true, min: 1 },
	inventoryQuantity: { label: 'Cantidad', integer: true, min: 0 },
	editInventoryQuantity: { label: 'Cantidad', integer: true, min: 0 },
	fecha_inicio: { label: 'Fecha de inicio', date: true },
	fecha_entrega: { label: 'Fecha de entrega', date: true },
};

const boundForms = new WeakSet();

const ruleFor = (field) => FIELD_RULES[field.id] || {};

const isBlankOptional = (field, rule) => rule.optional && field.value.trim() === '';

const validateText = (value, rule) => {
	if (rule.pattern && !rule.pattern.test(value)) return rule.patternMessage;
	if (rule.minLength && value.length < rule.minLength) return `Debe contener al menos ${rule.minLength} caracteres.`;
	if (rule.maxLength && value.length > rule.maxLength) return `No puede superar ${rule.maxLength} caracteres.`;
	return '';
};

const validateEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
	? ''
	: 'Ingresa un correo electrónico válido.';

const validateNumber = (field, value, rule) => {
	const number = Number(value);
	const min = rule.min ?? (field.min === '' ? null : Number(field.min));
	const max = field.max === '' ? null : Number(field.max);

	if (!Number.isFinite(number) || (rule.integer && !Number.isInteger(number))) {
		return rule.integer ? 'Ingresa un número entero.' : 'Ingresa un número válido.';
	}
	if (min !== null && number < min) return `El valor mínimo es ${min}.`;
	if (max !== null && number > max) return `El valor máximo es ${max}.`;
	return '';
};

const validateDate = (value) => Number.isNaN(Date.parse(value))
	? 'Ingresa una fecha válida.'
	: '';

const validateFile = (field) => {
	const file = field.files?.[0];
	if (!file) return field.required ? 'Selecciona un archivo.' : '';

	const acceptedTypes = (field.accept || 'image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp')
		.split(',')
		.map((item) => item.trim().toLowerCase());
	const extension = file.name.includes('.') ? `.${file.name.split('.').pop().toLowerCase()}` : '';
	const typeAllowed = acceptedTypes.some((type) => {
		if (type.startsWith('.')) return type === extension;
		if (type.endsWith('/*')) return file.type.toLowerCase().startsWith(type.slice(0, -1));
		return type === file.type.toLowerCase();
	});
	const maxSize = Number(field.dataset.maxSizeMb || 5) * 1024 * 1024;

	if (!typeAllowed) return 'El archivo debe ser JPG, PNG o WEBP.';
	if (file.size > maxSize) return `El archivo no puede superar ${field.dataset.maxSizeMb || 5} MB.`;
	return '';
};

const validateField = (field) => {
	if (field.disabled || field.type === 'hidden' || field.closest('[hidden], .d-none')) {
		field.setCustomValidity('');
		field.classList.remove('is-invalid', 'is-valid');
		return true;
	}

	const rule = ruleFor(field);
	const isNewBuildingRequired = field.id === 'newBuildingName'
		&& document.querySelector('#classroomBuilding')?.value === 'new';
	let message = '';
	const value = field.value.trim();

	if (field.type === 'file') {
		message = validateFile(field);
	} else if ((field.required || isNewBuildingRequired) && value === '') {
		message = 'Este campo es obligatorio.';
	} else if (!isBlankOptional(field, rule) && value !== '') {
		message = field.type === 'email'
			? validateEmail(value)
			: field.type === 'number'
				? validateNumber(field, value, rule)
				: rule.date
					? validateDate(value)
					: validateText(value, rule);
	}

	if (!message && field.id === 'newBuildingName' && document.querySelector('#classroomBuilding')?.value !== 'new') {
		message = '';
	}
	if (!message && field.id === 'fecha_entrega') {
		const startDate = document.querySelector('#fecha_inicio')?.value;
		if (startDate && value && value < startDate) {
			message = 'La fecha de entrega debe ser igual o posterior a la fecha de inicio.';
		}
	}

	field.setCustomValidity(message);
	field.classList.toggle('is-invalid', Boolean(message));
	field.classList.toggle('is-valid', !message && value !== '');
	field.setAttribute('aria-invalid', String(Boolean(message)));

	return message === '';
};

const sanitizeField = (field) => {
	const rule = ruleFor(field);
	if (!rule.pattern || !field.value) return;

	const originalValue = field.value;
	const sanitizedValue = [...originalValue].filter((character) => rule.pattern.test(character)).join('');
	if (sanitizedValue !== originalValue) {
		const selectionStart = field.selectionStart;
		field.value = sanitizedValue;
		if (typeof selectionStart === 'number') {
			field.setSelectionRange(Math.max(0, selectionStart - (originalValue.length - sanitizedValue.length)), Math.max(0, selectionStart - (originalValue.length - sanitizedValue.length)));
		}
	}
};

const connectFormValidation = (form) => {
	if (boundForms.has(form)) return;
	boundForms.add(form);

	form.querySelectorAll('input, select, textarea').forEach((field) => {
		const rule = ruleFor(field);
		if (rule.maxLength) field.maxLength = rule.maxLength;
		if (rule.minLength) field.minLength = rule.minLength;
		field.addEventListener('input', () => {
			sanitizeField(field);
			validateField(field);
		});
		field.addEventListener('change', () => {
			if (field.id === 'classroomBuilding') {
				const buildingName = document.querySelector('#newBuildingName');
				if (buildingName) validateField(buildingName);
			}
			validateField(field);
		});
		field.addEventListener('blur', () => validateField(field));
	});

	form.addEventListener('submit', (event) => {
		const fields = [...form.querySelectorAll('input, select, textarea')];
		const valid = fields.reduce((result, field) => validateField(field) && result, true);
		if (!valid) {
			event.preventDefault();
			event.stopImmediatePropagation();
			form.reportValidity();
		}
	}, true);
};

const connectAllFormValidations = () => {
	document.querySelectorAll('form').forEach(connectFormValidation);
};

if (typeof document !== 'undefined') {
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', connectAllFormValidations, { once: true });
	} else {
		connectAllFormValidations();
	}
}

export { connectAllFormValidations, connectFormValidation, validateDate, validateEmail, validateField, validateFile, validateForm, validateNumber, validateText };

function validateForm(form) {
	const fields = [...form.querySelectorAll('input, select, textarea')];
	const valid = fields.reduce((result, field) => validateField(field) && result, true);
	if (!valid) form.reportValidity();
	return valid;
}
