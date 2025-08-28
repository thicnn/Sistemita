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
    
    let searchDropdown = new bootstrap.Dropdown(clientSearchInput);

    // --- BÚSQUEDA DE CLIENTES (Funciona bien) ---
    clientSearchInput.addEventListener('keyup', async function () {
        const searchTerm = clientSearchInput.value.trim();
        searchResultsDiv.innerHTML = '';
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

    // --- LÓGICA DE ÍTEMS DEL PEDIDO (AHORA SÍ) ---

    const populateSelect = (select, options) => {
        select.innerHTML = '<option value="">Seleccionar...</option>';
        options.forEach(opt => select.add(new Option(opt, opt)));
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
            doble_faz: itemForm.querySelector('.doble_faz')
        };
        const groups = {
            maquina: itemForm.querySelector('.maquina-group'),
            categoria: itemForm.querySelector('.categoria-group')
        };
        
        const tipoVal = selects.tipo.value;
        const maquinaVal = selects.maquina.value;
        const categoriaVal = selects.categoria.value;

        // Lógica de reseteo y población en cascada
        if (target.classList.contains('tipo')) {
            const esServicio = tipoVal === 'Servicio';
            groups.maquina.style.display = esServicio ? 'none' : 'block';
            groups.categoria.style.display = esServicio ? 'none' : 'block';
            
            // Resetea todos los campos dependientes
            ['maquina', 'categoria', 'descripcion'].forEach(name => {
                selects[name].innerHTML = '';
                selects[name].disabled = true;
            });

            if (esServicio) {
                const descripciones = productsData.filter(p => p.tipo === 'Servicio').map(p => p.descripcion);
                populateSelect(selects.descripcion, descripciones);
                selects.descripcion.disabled = false;
            } else if (tipoVal) {
                const maquinas = [...new Set(productsData.filter(p => p.tipo === tipoVal).map(p => p.maquina_nombre))];
                populateSelect(selects.maquina, maquinas);
                selects.maquina.disabled = false;
            }
        } else if (target.classList.contains('maquina')) {
            ['categoria', 'descripcion'].forEach(name => {
                selects[name].innerHTML = '';
                selects[name].disabled = true;
            });
            if (maquinaVal) {
                const categorias = [...new Set(productsData.filter(p => p.tipo === tipoVal && p.maquina_nombre === maquinaVal).map(p => p.categoria))];
                populateSelect(selects.categoria, categorias);
                selects.categoria.disabled = false;
            }
        } else if (target.classList.contains('categoria')) {
            selects.descripcion.innerHTML = '';
            selects.descripcion.disabled = true;
            if (categoriaVal) {
                const descripciones = productsData.filter(p => p.tipo === tipoVal && p.maquina_nombre === maquinaVal && p.categoria === categoriaVal).map(p => p.descripcion);
                populateSelect(selects.descripcion, descripciones);
                selects.descripcion.disabled = false;
            }
        }
        
        // Habilitar campos finales
        const descSelected = !!selects.descripcion.value;
        selects.cantidad.disabled = !descSelected;
        selects.doble_faz.disabled = !descSelected;

        calculateTotals();
        validateForm();
    });
    
    // --- FUNCIONES GENERALES (Cálculo, Validación, Añadir/Quitar) ---

    const calculateTotals = () => {
        let totalGeneral = 0;
        itemsContainer.querySelectorAll('.item-form').forEach(itemForm => {
            const desc = itemForm.querySelector('.descripcion').value;
            const qty = parseInt(itemForm.querySelector('.cantidad').value) || 0;
            const product = productsData.find(p => p.descripcion === desc);
            const subtotal = (product && qty > 0) ? product.precio * qty : 0;
            itemForm.querySelector('.subtotal-item').textContent = subtotal.toFixed(2);
            totalGeneral += subtotal;
        });
        totalPedidoSpan.textContent = totalGeneral.toFixed(2);
    };

    const validateForm = () => {
        let isValid = !!clientHiddenInput.value;
        const items = itemsContainer.querySelectorAll('.item-form');
        if (items.length === 0) isValid = false;
        items.forEach(item => {
            if (!item.querySelector('.descripcion').value || !(parseInt(item.querySelector('.cantidad').value) > 0)) {
                isValid = false;
            }
        });
        submitButton.disabled = !isValid;
    };

    addItemBtn.addEventListener('click', () => {
        const newItem = template.content.cloneNode(true);
        const tipoSelect = newItem.querySelector('.tipo');
        populateSelect(tipoSelect, [...new Set(productsData.map(p => p.tipo))]);
        itemsContainer.appendChild(newItem);
        validateForm();
    });

    itemsContainer.addEventListener('input', e => {
        if (e.target.classList.contains('cantidad')) calculateTotals();
    });

    itemsContainer.addEventListener('click', e => {
        if (e.target.closest('.remove-item-btn')) {
            e.target.closest('.item-form').remove();
            calculateTotals();
            validateForm();
        }
    });

    orderForm.addEventListener('submit', e => {
        if (submitButton.disabled) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
        }
    });
    
    validateForm();
});