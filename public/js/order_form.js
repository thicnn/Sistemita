document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTOS DEL DOM ---
    const orderForm = document.getElementById('order-form');
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsContainer = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const totalPedidoSpan = document.getElementById('total-pedido');
    const submitButton = document.getElementById('submit-button');
    const clientSearchInput = document.getElementById('cliente_search');
    const clientHiddenInput = document.getElementById('cliente_id');
    const searchResultsDiv = document.getElementById('search-results');
    const descuentoTotalInput = document.getElementById('descuento-total');
    const discountAlertContainer = document.getElementById('discount-alert-container');

    let searchDropdown = new bootstrap.Dropdown(clientSearchInput);
    // Variable para guardar la información del descuento de fidelidad
    let loyaltyDiscountInfo = { amount: 0, requiredTotal: 0 };
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
                // Caso 1: El cliente TIENE el descuento disponible
                loyaltyDiscountInfo.amount = parseFloat((data.spent * 0.10).toFixed(2));
                loyaltyDiscountInfo.requiredTotal = parseFloat((loyaltyDiscountInfo.amount * 1.5).toFixed(2));

                alertHTML = `
                    <div class="alert alert-success d-flex flex-column mt-2 fade-in" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-star-fill me-2"></i>
                            <div>
                                ¡<strong>${clientName}</strong> tiene un descuento de <strong>$${loyaltyDiscountInfo.amount.toFixed(2)}</strong> este mes!
                            </div>
                        </div>
                        <div class="form-check form-switch mt-2 ms-4">
                          <input class="form-check-input" type="checkbox" role="switch" id="aplicar-descuento-fidelidad" name="aplicar_descuento_fidelidad" value="1">
                          <label class="form-check-label" for="aplicar-descuento-fidelidad"><strong>Aplicar este descuento al total</strong></label>
                        </div>
                        <div id="loyalty-validation-message" class="mt-2" style="display: none;"></div>
                    </div>
                `;
            } else if (data.already_used) {
                // Caso 2: El cliente YA USÓ el descuento este mes
                alertHTML = `<div class="alert alert-secondary d-flex align-items-center mt-2 fade-in" role="alert"><i class="bi bi-check-circle-fill me-2"></i><div><strong>${clientName}</strong> ya utilizó su descuento de este mes.</div></div>`;
            } else {
                // Caso 3: El cliente AÚN NO LLEGA a la meta
                const restante = metaDescuento - data.spent;
                alertHTML = `
                    <div class="alert alert-info d-flex align-items-center mt-2 fade-in" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div>
                            A <strong>${clientName}</strong> le faltan <strong>$${restante.toFixed(2)}</strong> para obtener su descuento mensual.
                        </div>
                    </div>
                `;
            }

            discountAlertContainer.innerHTML = alertHTML;
        } catch (error) {
            console.error('Error al verificar el descuento:', error);
        }
    };
    // --- BÚSQUEDA DE CLIENTES ---
    clientSearchInput.addEventListener('keyup', async function () {
        const searchTerm = clientSearchInput.value.trim();
        searchResultsDiv.innerHTML = '';
        discountAlertContainer.innerHTML = '';

        if (searchTerm.length < 2) {
            clientHiddenInput.value = '';
            searchDropdown.hide();
            validateForm();
            return;
        }
        try {
            const response = await fetch(`/sistemagestion/clients/search?term=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const clients = await response.json();
            if (clients.length > 0) {
                clients.forEach(client => {
                    const item = document.createElement('a');
                    item.classList.add('dropdown-item');
                    item.href = "#";
                    item.innerHTML = `<div class="client-name">${client.nombre}</div><div class="client-contact">${client.telefono || ''} &middot; ${client.email || ''}</div>`;
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        clientHiddenInput.value = client.id;
                        clientSearchInput.value = client.nombre;
                        searchDropdown.hide();
                        checkClientDiscount(client.id, client.nombre);
                        validateForm();
                    });
                    searchResultsDiv.appendChild(item);
                });
            } else {
                searchResultsDiv.innerHTML = '<span class="dropdown-item-text text-muted">No se encontraron clientes</span>';
            }
            searchDropdown.show();
        } catch (error) {
            console.error('Error al buscar clientes:', error);
            searchResultsDiv.innerHTML = '<span class="dropdown-item-text text-danger">Error al buscar</span>';
            searchDropdown.show();
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) searchDropdown.hide();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) searchDropdown.hide();
    });
    // --- LÓGICA DE ÍTEMS Y CÁLCULOS ---
    const populateSelect = (select, options) => {
        select.innerHTML = '<option value="">Seleccionar...</option>';
        [...new Set(options)].forEach(opt => select.add(new Option(opt, opt)));
    };

    const calculateTotals = () => {
        let subtotalGeneral = 0;
        itemsContainer.querySelectorAll('.item-form').forEach(itemForm => {
            const desc = itemForm.querySelector('.descripcion').value;
            const qty = parseInt(itemForm.querySelector('.cantidad').value) || 0;
            const descuentoPorcentaje = Math.min(100, parseFloat(itemForm.querySelector('.descuento-item').value) || 0);
            const product = productsData.find(p => p.descripcion === desc);

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
        let isValid = !!clientHiddenInput.value;
        const items = itemsContainer.querySelectorAll('.item-form');
        if (items.length === 0) {
            isValid = false;
        }
        items.forEach(item => {
            const desc = item.querySelector('.descripcion').value;
            const qty = parseInt(item.querySelector('.cantidad').value);
            if (!desc || !(qty > 0)) {
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



});