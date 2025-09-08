<h2 class="mb-4">Crear Nuevo Pedido</h2>

<script>
    // Codifica los datos de los productos para que estén disponibles en JavaScript
    const productsData = <?php echo json_encode($products); ?>;
</script>

<!-- Asistente de Pasos (Wizard) -->
<div id="order-wizard">
    <!-- Pestañas de Navegación -->
    <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-cliente-tab" data-bs-toggle="pill" data-bs-target="#pills-cliente" type="button" role="tab" aria-controls="pills-cliente" aria-selected="true">
                <span class="badge rounded-pill bg-primary me-2">1</span> Cliente
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-items-tab" data-bs-toggle="pill" data-bs-target="#pills-items" type="button" role="tab" aria-controls="pills-items" aria-selected="false" disabled>
                <span class="badge rounded-pill bg-primary me-2">2</span> Ítems del Pedido
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-resumen-tab" data-bs-toggle="pill" data-bs-target="#pills-resumen" type="button" role="tab" aria-controls="pills-resumen" aria-selected="false" disabled>
                <span class="badge rounded-pill bg-primary me-2">3</span> Resumen y Finalizar
            </button>
        </li>
    </ul>

    <form action="/sistemagestion/orders/create" method="POST" id="order-form">
        <!-- Contenido de las Pestañas -->
        <div class="tab-content" id="pills-tabContent">
            <!-- Paso 1: Cliente -->
            <div class="tab-pane fade show active" id="pills-cliente" role="tabpanel" aria-labelledby="pills-cliente-tab">
                <div class="card shadow-sm animated-card">
                    <div class="card-header bg-light"><h5 class="mb-0">Paso 1: Seleccionar Cliente</h5></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                             <div class="col-md-6">
                                <label for="cliente_search" class="form-label">Buscar y Asociar Cliente (o dejar en blanco para Consumidor Final)</label>
                                <div class="input-group">
                                    <input type="text" id="cliente_search" class="form-control dropdown-toggle" placeholder="Buscar por Nombre, Teléfono o Email..." data-bs-toggle="dropdown" autocomplete="off">
                                    <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#createClientModal"><i class="bi bi-person-plus-fill"></i></button>
                                    <input type="hidden" name="cliente_id" id="cliente_id">
                                    <div id="search-results" class="dropdown-menu w-100"></div>
                                </div>
                                <div id="discount-alert-container" class="mt-2"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado Inicial del Pedido</label>
                                <select name="estado" id="estado" class="form-select" required>
                                    <option value="Solicitud" selected>Solicitud</option>
                                    <option value="Cotización">Cotización</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="notas" class="form-label">Notas del Pedido (Opcional)</label>
                                <textarea name="notas" id="notas" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 2: Ítems -->
            <div class="tab-pane fade" id="pills-items" role="tabpanel" aria-labelledby="pills-items-tab">
                <div class="card shadow-sm animated-card">
                    <div class="card-header bg-light"><h5 class="mb-0">Paso 2: Buscar y Añadir Productos</h5></div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="product-search" class="form-label">Buscar producto por nombre, tipo o categoría</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="product-search" class="form-control" placeholder="Escribe para buscar...">
                            </div>
                            <div id="product-search-results" class="list-group mt-1" style="position: absolute; z-index: 1000; width: calc(100% - 2rem);"></div>
                        </div>
                        <hr class="my-4">
                        <h6 class="mb-3">Productos en el pedido:</h6>
                        <div id="items-container">
                             <!-- Los items añadidos aparecerán aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 3: Resumen -->
            <div class="tab-pane fade" id="pills-resumen" role="tabpanel" aria-labelledby="pills-resumen-tab">
                 <div class="card shadow-sm animated-card">
                    <div class="card-header bg-light"><h5 class="mb-0">Paso 3: Revisar y Confirmar</h5></div>
                    <div class="card-body p-4">
                        <div id="resumen-content" class="mb-4"></div>
                         <div class="text-end mt-3 border-top pt-3">
                            <div class="row justify-content-end align-items-center g-3 mb-3">
                                <div class="col-auto"><label for="descuento-total" class="col-form-label fs-5">Descuento Total (%):</label></div>
                                <div class="col-auto" style="max-width: 150px;">
                                    <div class="input-group">
                                        <input type="number" id="descuento-total" name="descuento_total" class="form-control" value="0" min="0" max="100" step="1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <h4 class="mb-0">Costo Total del Pedido: $<span id="total-pedido" class="fw-bold">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Navegación del Wizard -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" id="prev-btn" style="display: none;"><i class="bi bi-arrow-left me-2"></i>Anterior</button>
            <button type="button" class="btn btn-primary" id="next-btn"><i class="bi bi-arrow-right ms-2"></i>Siguiente</button>
            <button type="submit" id="submit-button" class="btn btn-primary btn-lg" style="display: none;"><i class="bi bi-save-fill me-2"></i>Guardar Pedido</button>
        </div>
    </form>
</div>

<style>
    #search-results .dropdown-item {
        white-space: normal;
        cursor: pointer;
    }

    #search-results .client-name {
        font-weight: 500;
    }

    #search-results .client-contact {
        font-size: 0.9em;
        color: var(--bs-secondary-color);
    }
</style>

<!-- Modal para Crear Cliente -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClientModalLabel">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="new-client-form">
                    <div class="mb-3">
                        <label for="new_client_nombre" class="form-label">Nombre Completo</label>
                        <input type="text" id="new_client_nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_client_telefono" class="form-label">Teléfono</label>
                            <input type="tel" id="new_client_telefono" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_client_email" class="form-label">Correo Electrónico</label>
                            <input type="email" id="new_client_email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_client_notas" class="form-label">Notas</label>
                        <textarea id="new_client_notas" name="notas" rows="3" class="form-control"></textarea>
                    </div>
                    <div id="new-client-error" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="save-new-client-btn" class="btn btn-primary">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const wizard = new bootstrap.Tab(document.getElementById('pills-cliente-tab'));
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-button');
    const tabs = document.querySelectorAll('#pills-tab .nav-link');
    const clientIdInput = document.getElementById('cliente_id');
    const itemsContainer = document.getElementById('items-container');

    let currentStep = 0;

    const validateStep = () => {
        if (currentStep === 0) {
            // Un cliente no es estrictamente necesario, puede ser consumidor final.
            // Siempre se puede pasar al siguiente paso.
            return true;
        }
        if (currentStep === 1) {
            return itemsContainer.children.length > 0;
        }
        return true;
    };

    const updateButtons = () => {
        prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = currentStep === tabs.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = currentStep === tabs.length - 1 ? 'inline-block' : 'none';

        nextBtn.disabled = !validateStep();
        submitBtn.disabled = !validateStep(); // También validar el último paso
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('show.bs.tab', () => {
            currentStep = index;
            // Habilitar todas las pestañas hasta la actual
            for(let i = 0; i <= currentStep; i++) {
                tabs[i].disabled = false;
            }
            updateButtons();
        });
    });

    nextBtn.addEventListener('click', () => {
        if (currentStep < tabs.length - 1) {
            tabs[currentStep + 1].disabled = false;
            const nextTab = new bootstrap.Tab(tabs[currentStep + 1]);
            nextTab.show();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            const prevTab = new bootstrap.Tab(tabs[currentStep - 1]);
            prevTab.show();
        }
    });

    // Observar cambios en el contenedor de items para revalidar
    const itemsObserver = new MutationObserver(updateButtons);
    itemsObserver.observe(itemsContainer, { childList: true, subtree: true });

    // Generar resumen en el último paso
    document.getElementById('pills-resumen-tab').addEventListener('show.bs.tab', function () {
        const clienteNombre = document.getElementById('cliente_search').value || 'Consumidor Final';
        const items = document.querySelectorAll('.item-form');
        let resumenHtml = `<p><strong>Cliente:</strong> ${clienteNombre}</p><h6>Ítems:</h6><ul class="list-group">`;
        if (items.length > 0) {
            items.forEach(item => {
                const desc = item.querySelector('.descripcion option:checked').text;
                const cant = item.querySelector('.cantidad').value;
                const subtotal = item.querySelector('.subtotal-item').textContent;
                resumenHtml += `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${cant} x ${desc}</span> <strong>$${subtotal}</strong></li>`;
            });
        } else {
            resumenHtml += `<li class="list-group-item">No se han añadido ítems.</li>`;
        }
        resumenHtml += `</ul>`;
        document.getElementById('resumen-content').innerHTML = resumenHtml;
    });

    updateButtons(); // Estado inicial
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSearchInput = document.getElementById('product-search');
    const productSearchResults = document.getElementById('product-search-results');
    const itemsContainer = document.getElementById('items-container');

    // Función para añadir un item desde la búsqueda
    const addItemFromSearch = (product) => {
        const itemTemplate = `
            <div class="item-form border rounded p-3 mb-3 bg-body-tertiary fade-in">
                <input type="hidden" class="producto-id" name="items[producto_id][]" value="${product.id}">
                <p class="mb-1"><strong>${product.descripcion}</strong></p>
                <small class="text-muted">${product.tipo} / ${product.categoria}</small>
                <div class="row align-items-end g-3 mt-1">
                    <div class="col">
                        <label class="form-label">Cantidad</label>
                        <input type="number" class="form-control item-selector cantidad" name="items[cantidad][]" min="1" value="1">
                    </div>
                    <div class="col">
                        <label class="form-label">Desc. (%)</label>
                        <input type="number" class="form-control item-selector descuento-item" name="items[descuento][]" value="0" min="0" max="100">
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-selector doble_faz" name="items[doble_faz][]" value="1">
                            <label class="form-check-label">Doble Faz</label>
                        </div>
                    </div>
                     <div class="col-auto d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="bi bi-trash-fill"></i></button>
                    </div>
                </div>
                <div class="text-end mt-2">
                    <strong>Subtotal: $<span class="subtotal-item">0.00</span></strong>
                </div>
            </div>
        `;
        itemsContainer.insertAdjacentHTML('beforeend', itemTemplate);

        // Disparar un evento 'change' para que el script principal recalcule
        itemsContainer.dispatchEvent(new Event('change'));
    };

    productSearchInput.addEventListener('keyup', async (e) => {
        const searchTerm = e.target.value;
        productSearchResults.innerHTML = '';

        if (searchTerm.length < 2) return;

        const response = await fetch(`/sistemagestion/orders/search-products-ajax?term=${searchTerm}`);
        const products = await response.json();

        if (products.length > 0) {
            products.forEach(product => {
                const item = document.createElement('a');
                item.href = '#';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${product.descripcion}</h6>
                        <small>$${product.precio}</small>
                    </div>
                    <p class="mb-1 text-muted">${product.tipo} / ${product.categoria}</p>
                `;
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    addItemFromSearch(product);
                    productSearchInput.value = '';
                    productSearchResults.innerHTML = '';
                });
                productSearchResults.appendChild(item);
            });
        } else {
            productSearchResults.innerHTML = '<span class="list-group-item text-muted">No se encontraron productos.</span>';
        }
    });

    // Ocultar resultados si se hace clic fuera
    document.addEventListener('click', function(e) {
        if (!productSearchInput.contains(e.target)) {
            productSearchResults.innerHTML = '';
        }
    });
});
</script>

<script>
// I am removing the template tag as it is no longer used by the new search functionality.
// The new JS logic creates the item HTML dynamically.
</script>
<script src="/sistemagestion/public/js/order_form.js"></script>