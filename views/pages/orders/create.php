<h2 class="mb-4">Crear Nuevo Pedido</h2>

<script>
    const productsData = <?php echo json_encode($products); ?>;
</script>

<form action="/sistemagestion/orders/create" method="POST" id="order-form">
    <div class="card shadow-sm mb-4 animated-card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i>1. Datos Principales</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cliente_search" class="form-label">Buscar y Asociar Cliente</label>
                    <div class="dropdown">
                        <input type="text" id="cliente_search" class="form-control dropdown-toggle" placeholder="Buscar por Nombre, Teléfono o Email..." data-bs-toggle="dropdown" autocomplete="off">
                        <input type="hidden" name="cliente_id" id="cliente_id">
                        <div id="search-results" class="dropdown-menu w-100"></div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
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

    <div class="card shadow-sm animated-card" style="animation-delay: 0.1s;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>2. Ítems del Pedido</h5>
            <button type="button" id="add-item-btn" class="btn btn-success">
                <i class="bi bi-plus-circle me-2"></i>Añadir Ítem
            </button>
        </div>
        <div class="card-body p-4">
            <div id="items-container">
                </div>
            <div class="text-end mt-3 border-top pt-3">
                <h4 class="mb-0">Costo Total del Pedido: $<span id="total-pedido" class="fw-bold">0.00</span></h4>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center animated-card" style="animation-delay: 0.2s;">
        <button type="submit" id="submit-button" class="btn btn-primary btn-lg" disabled>
            <i class="bi bi-save-fill me-2"></i>Guardar Pedido
        </button>
    </div>
</form>

<template id="item-template">
    <div class="item-form border rounded p-3 mb-3 bg-light fade-in">
        <div class="row align-items-end g-3">
            <div class="col-lg-2 col-md-4"><label class="form-label">Tipo</label><select class="form-select item-selector tipo" name="items[tipo][]"></select></div>
            <div class="col-lg-2 col-md-4 maquina-group" style="display: none;"><label class="form-label">Máquina</label><select class="form-select item-selector maquina" name="items[maquina][]" disabled></select></div>
            <div class="col-lg-2 col-md-4"><label class="form-label">Categoría</label><select class="form-select item-selector categoria" name="items[categoria][]" disabled></select></div>
            <div class="col-lg-3 col-md-6"><label class="form-label">Descripción</label><select class="form-select item-selector descripcion" name="items[descripcion][]" disabled></select></div>
            <div class="col-lg-1 col-md-2"><label class="form-label">Cantidad</label><input type="number" class="form-control item-selector cantidad" name="items[cantidad][]" min="1" value="1" disabled></div>
            <div class="col-lg-2 col-md-4 d-flex align-items-center pt-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input item-selector doble_faz" name="items[doble_faz][]" value="1" disabled>
                    <label class="form-check-label">Doble Faz</label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="bi bi-trash-fill"></i></button>
            <strong class="fs-5">Subtotal: $<span class="subtotal-item">0.00</span></strong>
        </div>
    </div>
</template>

<style>
/* Estilos para que el buscador de clientes se vea perfecto */
#search-results .dropdown-item {
    white-space: normal;
    cursor: pointer;
}
#search-results .client-name {
    font-weight: 500;
}
#search-results .client-contact {
    font-size: 0.9em;
    color: #6c757d;
}
</style>

<script src="/sistemagestion/public/js/order_form.js"></script>