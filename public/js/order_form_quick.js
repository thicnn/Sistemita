document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTOS DEL DOM ---
    const orderForm = document.getElementById('order-form');
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsContainer = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const totalPedidoSpan = document.getElementById('total-pedido');
    const submitButton = document.getElementById('submit-button');
    const descuentoTotalInput = document.getElementById('descuento-total');
    const estadoSelect = document.getElementById('estado');
    const pagoFinalContainer = document.getElementById('pago-final-container');
    const pagoRadios = document.querySelectorAll('input[name="metodo_pago_final"]');

    function togglePagoFinal() {
        const esEntregado = estadoSelect.value === 'Entregado';
        const total = parseFloat(totalPedidoSpan.textContent);

        if (esEntregado && total > 0) {
            pagoFinalContainer.style.display = 'block';
            pagoRadios.forEach(radio => radio.required = true);
            if (!document.querySelector('input[name="metodo_pago_final"]:checked')) {
                document.getElementById('pago_efectivo').checked = true;
            }
        } else {
            pagoFinalContainer.style.display = 'none';
            pagoRadios.forEach(radio => {
                radio.required = false;
                radio.checked = false;
            });
        }
    }

    if (estadoSelect) {
        estadoSelect.addEventListener('change', togglePagoFinal);
    }

    const observer = new MutationObserver(togglePagoFinal);
    observer.observe(totalPedidoSpan, { childList: true, subtree: true });

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
                if (tipoVal === 'Servicio') {
                    return true;
                } else {
                    return p.maquina_nombre === maquinaNombre;
                }
            });

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
        let isValid = true; // No client to validate, so start with true
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

    orderForm.addEventListener('submit', e => {
        if (submitButton.disabled) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos antes de guardar.');
        }
    });

    // Estado inicial
    validateForm();
});
