<h2 class="mb-4">Pedido Rápido</h2>

<script>
    // Codifica los datos de los productos para que estén disponibles en JavaScript
    const productsData = <?php echo json_encode($products); ?>;
</script>

<form action="/sistemagestion/orders/store_quick" method="POST" id="order-form">
    <div class="card shadow-sm mb-4 animated-card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>1. Datos del Pedido</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="estado" class="form-label">Estado Inicial del Pedido</label>
                    <select name="estado" id="estado" class="form-select" required>
                        <option value="Solicitud">Solicitud</option>
                        <option value="Cotización">Cotización</option>
                        <option value="Confirmado">Confirmado</option>
                        <option value="En curso">En curso</option>
                        <option value="Listo para entregar">Listo para entregar</option>
                        <option value="Entregado">Entregado</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="notas" class="form-label">Notas del Pedido (Opcional)</label>
                    <textarea name="notas" id="notas" rows="1" class="form-control"></textarea>
                </div>
                <div class="col-12 mt-2">
                    <div class="form-check form-switch d-inline-block me-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="es_interno" name="es_interno" value="1">
                        <label class="form-check-label" for="es_interno">Marcar como Uso Interno</label>
                    </div>
                    <div class="form-check form-switch d-inline-block">
                        <input class="form-check-input" type="checkbox" role="switch" id="es_error" name="es_error" value="1">
                        <label class="form-check-label" for="es_error">Marcar como Error</label>
                    </div>
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
                <div class="row justify-content-end align-items-center g-3 mb-3">
                    <div class="col-auto">
                        <label for="descuento-total" class="col-form-label fs-5">Descuento Total (%):</label>
                    </div>
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

    <div class="mt-4 text-center animated-card" style="animation-delay: 0.2s;">
        <button type="submit" id="submit-button" class="btn btn-primary btn-lg" disabled>
            <i class="bi bi-save-fill me-2"></i>Guardar Pedido
        </button>
    </div>
</form>

<template id="item-template">
    <div class="item-form border rounded p-3 mb-3 bg-body-tertiary fade-in">
        <input type="hidden" class="producto-id" name="items[producto_id][]">
        <div class="row align-items-end g-3">
            <div class="col-lg-2 col-md-4">
                <label class="form-label">Tipo</label>
                <select class="form-select item-selector tipo" name="items[tipo][]"></select>
            </div>
            <div class="col-lg-2 col-md-4 maquina-group" style="display: none;">
                <label class="form-label">Máquina</label>
                <select class="form-select item-selector maquina" name="items[maquina][]" disabled></select>
            </div>
            <div class="col-lg-2 col-md-4 categoria-group" style="display: none;">
                <label class="form-label">Categoría</label>
                <select class="form-select item-selector categoria" name="items[categoria][]" disabled></select>
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Descripción</label>
                <select class="form-select item-selector descripcion" name="items[descripcion][]" disabled></select>
            </div>
            <div class="col-lg-1 col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control item-selector cantidad" name="items[cantidad][]" min="1" value="1" disabled>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label">Desc. (%)</label>
                <div class="input-group">
                    <input type="number" class="form-control item-selector descuento-item" name="items[descuento][]" min="0" max="100" value="0" step="1" disabled>
                    <span class="input-group-text">%</span>
                </div>
            </div>
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

<script src="/sistemagestion/public/js/order_form_quick.js"></script>
