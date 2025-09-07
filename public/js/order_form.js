document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTOS DEL DOM ---
    const orderForm = document.getElementById('order-form');
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsContainer = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const totalPedidoSpan = document.getElementById('total-pedido');
    const submitButton = document.getElementById('submit-button');
    const descuentoTotalInput = document.getElementById('descuento-total');
    const discountAlertContainer = document.getElementById('discount-alert-container');
    let loyaltyDiscountInfo = { amount: 0, requiredTotal: 0 };

    const tomSelect = new TomSelect('#cliente_search', {
        valueField: 'id',
        labelField: 'nombre',
        searchField: ['nombre', 'telefono', 'email'],
        create: true,
        render: {
            option: function(data, escape) {
                return `<div class="d-flex">
                            <div>
                                <div class="text-dark">${escape(data.nombre)}</div>
                                <div class="text-muted small">${escape(data.telefono) || ''} &middot; ${escape(data.email) || ''}</div>
                            </div>
                        </div>`;
            },
            item: function(data, escape) {
                return `<div>${escape(data.nombre)}</div>`;
            }
        },
        load: function(query, callback) {
            if (query.length < 2) return callback();
            fetch(`/sistemagestion/clients/search?term=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(json => {
                    callback(json);
                }).catch(()=>{
                    callback();
                });
        },
        onChange: function(value) {
            if (value) {
                const clientName = this.options[value].nombre;
                checkClientDiscount(value, clientName);
            } else {
                discountAlertContainer.innerHTML = '';
            }
            validateForm();
        },
        onCreate: function(input) {
            const modal = new bootstrap.Modal(document.getElementById('createClientModal'));
            const modalNameInput = document.getElementById('new_client_nombre');
            if (modalNameInput) {
                modalNameInput.value = input;
            }
            modal.show();
            return false; // Prevent Tom Select from adding the item
        }
    });

    // --- LÓGICA DE ALERTA DE DESCUENTO ---
    const checkClientDiscount = async (clientId, clientName) => {
        discountAlertContainer.innerHTML = '';
        loyaltyDiscountInfo = { amount: 0, requiredTotal: 0 }; // Reset
        if (!clientId) return;

        try {
            const response = await fetch(`/sistemagestion/clients/check_discount/${clientId}`);
            const data = await response.json();
            let alertHTML = '';
            const metaDescuento = 300;

            if (data.eligible) {
                loyaltyDiscountInfo.amount = parseFloat((data.spent * 0.10).toFixed(2));
                loyaltyDiscountInfo.requiredTotal = parseFloat((loyaltyDiscountInfo.amount * 1.5).toFixed(2));
                alertHTML = `...`; // Same as before
            } else if (data.already_used) {
                alertHTML = `...`; // Same as before
            } else {
                const restante = metaDescuento - data.spent;
                alertHTML = `...`; // Same as before
            }

            discountAlertContainer.innerHTML = alertHTML;
        } catch (error) {
            console.error('Error al verificar el descuento:', error);
        }
    };
    // --- LÓGICA DE ÍTEMS Y CÁLCULOS ---
    const populateSelect = (select, options) => {
        select.innerHTML = '<option value="">Seleccionar...</option>';
        [...new Set(options)].forEach(opt => select.add(new Option(opt, opt)));
    };

    const calculateTotals = () => {
        let subtotalGeneral = 0;
        itemsContainer.querySelectorAll('.item-form').forEach(itemForm => {
            const tipoVal = itemForm.querySelector('.tipo').value;
            const desc = itemForm.querySelector('.descripcion').value;
            const qty = parseInt(itemForm.querySelector('.cantidad').value) || 0;
            const descuentoPorcentaje = Math.min(100, parseFloat(itemForm.querySelector('.descuento-item').value) || 0);
            const maquinaNombre = itemForm.querySelector('.maquina').value;

            const product = productsData.find(p => {
                if (p.descripcion !== desc || p.tipo !== tipoVal) {
                    return false;
                }
                // Para servicios, solo importa la descripción y el tipo.
                // Para otros, también debe coincidir la máquina.
                if (tipoVal === 'Servicio') {
                    return true;
                } else {
                    return p.maquina_nombre === maquinaNombre;
                }
            });

            // Seteamos el ID del producto en el campo oculto
            itemForm.querySelector('.producto-id').value = product ? product.id : '';

            const subtotalBruto = (product && qty > 0) ? product.precio * qty : 0;
            const montoDescuento = subtotalBruto * (descuentoPorcentaje / 100);
            const subtotalNeto = subtotalBruto - montoDescuento;

            itemForm.querySelector('.subtotal-item').textContent = subtotalNeto.toFixed(2);
            subtotalGeneral += subtotalNeto;
        });

        const descuentoManualPorcentaje = Math.min(100, parseFloat(descuentoTotalInput.value) || 0);
        const montoDescuentoManual = subtotalGeneral * (descuentoManualPorcentaje / 100);
        let totalFinal = subtotalGeneral - montoDescuentoManual;

        const aplicarFidelidadCheck = document.getElementById('aplicar-descuento-fidelidad');
        const validationMessageDiv = document.getElementById('loyalty-validation-message');

        if (aplicarFidelidadCheck && aplicarFidelidadCheck.checked) {
            if (totalFinal >= loyaltyDiscountInfo.requiredTotal) {
                totalFinal -= loyaltyDiscountInfo.amount;
                if (validationMessageDiv) {
                    validationMessageDiv.style.display = 'block';
                    validationMessageDiv.className = 'alert alert-info py-1 px-2 mt-2 ms-4';
                    validationMessageDiv.innerHTML = `<i class="bi bi-check-circle-fill"></i> Descuento de <strong>$${loyaltyDiscountInfo.amount.toFixed(2)}</strong> aplicado.`;
                }
            } else {
                if (validationMessageDiv) {
                    validationMessageDiv.style.display = 'block';
                    validationMessageDiv.className = 'alert alert-warning py-1 px-2 mt-2 ms-4';
                    validationMessageDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> El total debe ser de al menos <strong>$${loyaltyDiscountInfo.requiredTotal.toFixed(2)}</strong> para usar este descuento.`;
                }
            }
        } else {
            if (validationMessageDiv) validationMessageDiv.style.display = 'none';
        }

        totalPedidoSpan.textContent = totalFinal.toFixed(2);
    };

    itemsContainer.addEventListener('change', e => {
        const target = e.target;
        if (!target.classList.contains('item-selector')) return;

        const itemForm = target.closest('.item-form');
        const selects = {
            tipo: itemForm.querySelector('.tipo'),
            maquina: itemForm.querySelector('.maquina'),
            categoria: itemForm.querySelector('.categoria'),
            descripcion: itemForm.querySelector('.descripcion'),
            cantidad: itemForm.querySelector('.cantidad'),
            doble_faz: itemForm.querySelector('.doble_faz'),
            descuento: itemForm.querySelector('.descuento-item')
        };
        const groups = {
            maquina: itemForm.querySelector('.maquina-group'),
            categoria: itemForm.querySelector('.categoria-group')
        };

        const tipoVal = selects.tipo.value;
        const maquinaVal = selects.maquina.value;

        if (target.classList.contains('tipo')) {
            const esServicio = tipoVal === 'Servicio';
            groups.maquina.style.display = esServicio ? 'none' : 'block';
            groups.categoria.style.display = esServicio ? 'none' : 'block';

            ['maquina', 'categoria', 'descripcion'].forEach(name => {
                selects[name].innerHTML = '<option value="">Seleccionar...</option>';
                selects[name].disabled = true;
            });

            if (esServicio) {
                populateSelect(selects.descripcion, productsData.filter(p => p.tipo === 'Servicio').map(p => p.descripcion));
                selects.descripcion.disabled = false;
            } else if (tipoVal) {
                populateSelect(selects.maquina, productsData.filter(p => p.tipo === tipoVal).map(p => p.maquina_nombre));
                selects.maquina.disabled = false;
            }
        } else if (target.classList.contains('maquina')) {
            ['categoria', 'descripcion'].forEach(name => {
                selects[name].innerHTML = '<option value="">Seleccionar...</option>';
                selects[name].disabled = true;
            });
            if (maquinaVal) {
                populateSelect(selects.categoria, productsData.filter(p => p.tipo === tipoVal && p.maquina_nombre === maquinaVal).map(p => p.categoria));
                selects.categoria.disabled = false;
            }
        } else if (target.classList.contains('categoria')) {
            selects.descripcion.innerHTML = '<option value="">Seleccionar...</option>';
            selects.descripcion.disabled = true;
            const categoriaVal = selects.categoria.value;
            if (categoriaVal) {
                populateSelect(selects.descripcion, productsData.filter(p => p.tipo === tipoVal && p.maquina_nombre === maquinaVal && p.categoria === categoriaVal).map(p => p.descripcion));
                selects.descripcion.disabled = false;
            }
        }

        const descSelected = !!selects.descripcion.value;
        selects.cantidad.disabled = !descSelected;
        selects.doble_faz.disabled = !descSelected;
        selects.descuento.disabled = !descSelected;

        calculateTotals();
        validateForm();
    });

    // --- MANEJO GENERAL DEL FORMULARIO ---
    const validateForm = () => {
        let isValid = !!tomSelect.getValue();
        const items = itemsContainer.querySelectorAll('.item-form');
        if (items.length === 0) {
            isValid = false;
        }
        items.forEach(item => {
            const productoId = item.querySelector('.producto-id').value;
            const qty = parseInt(item.querySelector('.cantidad').value);
            if (!productoId || !(qty > 0)) {
                isValid = false;
            }
        });
        submitButton.disabled = !isValid;
    };

    addItemBtn.addEventListener('click', () => {
        const newItem = template.content.cloneNode(true);
        const tipoSelect = newItem.querySelector('.tipo');
        populateSelect(tipoSelect, productsData.map(p => p.tipo));
        itemsContainer.appendChild(newItem);
        validateForm();
    });

    itemsContainer.addEventListener('input', e => {
        if (e.target.classList.contains('cantidad') || e.target.classList.contains('descuento-item')) {
            calculateTotals();
        }
    });

    descuentoTotalInput.addEventListener('input', calculateTotals);

    itemsContainer.addEventListener('click', e => {
        if (e.target.closest('.remove-item-btn')) {
            e.target.closest('.item-form').remove();
            calculateTotals();
            validateForm();
        }
    });
    discountAlertContainer.addEventListener('change', e => {
        if (e.target.id === 'aplicar-descuento-fidelidad') {
            calculateTotals();
        }
    });
    orderForm.addEventListener('submit', e => {
        if (submitButton.disabled) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos antes de guardar.');
        }
    });

    // Estado inicial
    validateForm();

    // --- LÓGICA PARA CREAR CLIENTE EN MODAL ---
    const saveNewClientBtn = document.getElementById('save-new-client-btn');
    const newClientForm = document.getElementById('new-client-form');
    const createClientModalEl = document.getElementById('createClientModal');
    const createClientModal = new bootstrap.Modal(createClientModalEl);
    const newClientErrorDiv = document.getElementById('new-client-error');

    saveNewClientBtn.addEventListener('click', async () => {
        const formData = new FormData(newClientForm);
        newClientErrorDiv.style.display = 'none';
        newClientErrorDiv.textContent = '';

        try {
            const response = await fetch('/sistemagestion/clients/create_ajax', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                createClientModal.hide();
                newClientForm.reset();
                tomSelect.addOption(result.client);
                tomSelect.setValue(result.client.id);
            } else {
                newClientErrorDiv.textContent = result.message || 'Ocurrió un error.';
                newClientErrorDiv.style.display = 'block';
            }
        } catch (error) {
            console.error('Error al crear cliente:', error);
            newClientErrorDiv.textContent = 'Error de conexión. Inténtalo de nuevo.';
            newClientErrorDiv.style.display = 'block';
        }
    });

});