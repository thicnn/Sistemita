document.addEventListener('DOMContentLoaded', function () {
    // --- SELECCIÓN DE ELEMENTOS (sin cambios) ---
    const orderForm = document.getElementById('order-form');
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsContainer = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const totalPedidoSpan = document.getElementById('total-pedido');
    const submitButton = document.getElementById('submit-button');
    const clientSearchInput = document.getElementById('cliente_search');
    const clientHiddenInput = document.getElementById('cliente_id');
    const searchResultsDiv = document.getElementById('search-results');
    
    // Instancia del Dropdown de Bootstrap para control manual
    let searchDropdown = new bootstrap.Dropdown(clientSearchInput);

    // --- LÓGICA DE BÚSQUEDA DE CLIENTES (COMPLETAMENTE RENOVADA) ---
    clientSearchInput.addEventListener('keyup', async function () {
        const searchTerm = clientSearchInput.value.trim();
        searchResultsDiv.innerHTML = ''; // Limpiamos resultados anteriores

        if (searchTerm.length < 2) {
            clientHiddenInput.value = ''; // Limpiamos el ID si se borra la búsqueda
            searchDropdown.hide(); // Ocultamos el dropdown si no hay búsqueda
            validateForm();
            return;
        }

        try {
            const response = await fetch(`/sistemagestion/clients/search?term=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error('Network response was not ok');
            
            const clients = await response.json();

            if (clients.length > 0) {
                clients.forEach(client => {
                    const resultItem = document.createElement('a');
                    resultItem.classList.add('dropdown-item');
                    resultItem.href = "#";
                    // Creamos el HTML para cada resultado, mostrando nombre, teléfono y email
                    resultItem.innerHTML = `
                        <div class="client-name">${client.nombre}</div>
                        <div class="client-contact">${client.telefono || ''} &middot; ${client.email || ''}</div>
                    `;
                    
                    // Evento de clic para seleccionar un cliente
                    resultItem.addEventListener('click', (e) => {
                        e.preventDefault();
                        clientHiddenInput.value = client.id;
                        clientSearchInput.value = client.nombre;
                        searchDropdown.hide(); // Ocultamos el dropdown al seleccionar
                        validateForm();
                    });
                    searchResultsDiv.appendChild(resultItem);
                });
            } else {
                searchResultsDiv.innerHTML = '<span class="dropdown-item-text text-muted">No se encontraron clientes</span>';
            }
            searchDropdown.show(); // Mostramos el dropdown con los resultados
        } catch (error) {
            console.error('Error fetching clients:', error);
            searchResultsDiv.innerHTML = '<span class="dropdown-item-text text-danger">Error al buscar</span>';
            searchDropdown.show();
        }
    });

    // Ocultar resultados si se hace clic fuera del buscador
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            searchDropdown.hide();
        }
    });


    // --- EL RESTO DEL CÓDIGO (VALIDACIÓN, CÁLCULOS, ETC.) SE MANTIENE IGUAL ---
    
    const validateForm = () => {
        let isFormValid = true;
        if (!clientHiddenInput.value) isFormValid = false;

        const items = itemsContainer.querySelectorAll('.item-form');
        if (items.length === 0) isFormValid = false;

        items.forEach(item => {
            const desc = item.querySelector('.descripcion').value;
            const qty = parseInt(item.querySelector('.cantidad').value);
            if (!desc || qty < 1) isFormValid = false;
        });

        submitButton.disabled = !isFormValid;
    };
    
    const calculateTotals = () => {
        let totalGeneral = 0;
        itemsContainer.querySelectorAll('.item-form').forEach(itemForm => {
            const selectedDescripcion = itemForm.querySelector('.descripcion').value;
            const cantidadCarillas = parseInt(itemForm.querySelector('.cantidad').value) || 0;
            const product = productsData.find(p => p.descripcion === selectedDescripcion);
            let subtotal = 0;
            if (product && cantidadCarillas > 0) {
                subtotal = product.precio * cantidadCarillas;
            }
            itemForm.querySelector('.subtotal-item').textContent = subtotal.toFixed(2);
            totalGeneral += subtotal;
        });
        totalPedidoSpan.textContent = totalGeneral.toFixed(2);
    };

    const populateSelect = (select, options, selectedValue = '') => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Seleccionar...</option>';
        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt;
            option.textContent = opt;
            select.appendChild(option);
        });
        select.value = options.includes(currentValue) ? currentValue : selectedValue;
    };
    
    const updateItemState = (itemForm) => {
        const selects = {
            tipo: itemForm.querySelector('.tipo'),
            maquina: itemForm.querySelector('.maquina'),
            cat: itemForm.querySelector('.categoria'),
            desc: itemForm.querySelector('.descripcion'),
            qty: itemForm.querySelector('.cantidad'),
            faz: itemForm.querySelector('.doble_faz')
        };
        const maquinaGroup = itemForm.querySelector('.maquina-group');

        const values = {
            tipo: selects.tipo.value,
            maquina: selects.maquina.value,
            cat: selects.cat.value
        };

        if (values.tipo && values.tipo !== 'Servicio') {
            maquinaGroup.style.display = 'block';
            selects.maquina.disabled = false;
            const maquinas = [...new Set(productsData.filter(p => p.tipo === values.tipo).map(p => p.maquina_nombre))];
            populateSelect(selects.maquina, maquinas, values.maquina);
        } else {
            maquinaGroup.style.display = 'none';
            selects.maquina.disabled = true;
            selects.maquina.value = '';
        }

        const canLoadCategories = values.tipo && (values.tipo === 'Servicio' || (values.tipo !== 'Servicio' && values.maquina));
        selects.cat.disabled = !canLoadCategories;
        if (canLoadCategories) {
            const cats = [...new Set(productsData
                .filter(p => p.tipo === values.tipo && (values.tipo === 'Servicio' || p.maquina_nombre === values.maquina))
                .map(p => p.categoria))];
            populateSelect(selects.cat, cats, values.cat);
        }

        const canLoadDescriptions = canLoadCategories && values.cat;
        selects.desc.disabled = !canLoadDescriptions;
        if (canLoadDescriptions) {
            const descs = productsData
                .filter(p => p.tipo === values.tipo && (values.tipo === 'Servicio' || p.maquina_nombre === values.maquina) && p.categoria === values.cat)
                .map(p => p.descripcion);
            populateSelect(selects.desc, descs);
        }
        
        const allSelected = selects.desc.value;
        selects.qty.disabled = !allSelected;
        selects.faz.disabled = !allSelected;

        calculateTotals();
        validateForm();
    };

    addItemBtn.addEventListener('click', () => {
        const newItem = template.content.cloneNode(true);
        const tipoSelect = newItem.querySelector('.tipo');
        populateSelect(tipoSelect, [...new Set(productsData.map(p => p.tipo))]);
        itemsContainer.appendChild(newItem);
        validateForm();
    });

    itemsContainer.addEventListener('change', e => {
        if (e.target.classList.contains('item-selector')) {
            const itemForm = e.target.closest('.item-form');
            if (e.target.classList.contains('tipo')) {
                itemForm.querySelector('.maquina').value = '';
                itemForm.querySelector('.categoria').value = '';
                itemForm.querySelector('.descripcion').value = '';
            }
            if (e.target.classList.contains('maquina')) {
                itemForm.querySelector('.categoria').value = '';
                itemForm.querySelector('.descripcion').value = '';
            }
            if (e.target.classList.contains('categoria')) {
                itemForm.querySelector('.descripcion').value = '';
            }
            updateItemState(itemForm);
        }
    });
    
    itemsContainer.addEventListener('input', e => {
         if (e.target.classList.contains('cantidad')) {
            calculateTotals();
        }
    });

    itemsContainer.addEventListener('click', e => {
        if (e.target.classList.contains('remove-item-btn')) {
            e.target.closest('.item-form').remove();
            calculateTotals();
            validateForm();
        }
    });

    orderForm.addEventListener('change', validateForm);
    orderForm.addEventListener('submit', e => {
        if (submitButton.disabled) {
            e.preventDefault();
            alert('Por favor, complete todos los campos requeridos para guardar el pedido.');
        }
    });

    validateForm();
});