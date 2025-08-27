<h2 class="mb-4">Editando Pedido #<?php echo $order['id']; ?></h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/sistemagestion/orders/edit/<?php echo $order['id']; ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Cliente:</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?>" disabled readonly>
            </div>
            <div class="mb-3">
                <label for="estado" class="form-label">Cambiar Estado:</label>
                <select name="estado" id="estado" class="form-select" required <?php echo $order['estado'] === 'Cancelado' ? 'disabled' : ''; ?>>
                    <?php
                    $estados = ["Solicitud", "Cotización", "Confirmado", "En Curso", "Listo para Retirar", "Entregado"];
                    foreach ($estados as $estado) {
                        $selected = ($order['estado'] == $estado) ? 'selected' : '';
                        echo "<option value='{$estado}' {$selected}>{$estado}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="notas" class="form-label">Notas del Pedido:</label>
                <textarea name="notas" id="notas" rows="3" class="form-control" <?php echo $order['estado'] === 'Cancelado' ? 'disabled' : ''; ?>><?php echo htmlspecialchars($order['notas_internas'] ?? ''); ?></textarea>
            </div>

            <hr class="my-4">

            <?php if ($order['estado'] !== 'Cancelado'): ?>
                <div class="cancel-section bg-light border border-danger-subtle rounded p-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="cancelar-checkbox">
                        <label class="form-check-label fw-bold text-danger" for="cancelar-checkbox">
                            Cancelar Pedido
                        </label>
                    </div>
                    <div class="mt-2" id="motivo-container" style="display:none;">
                        <label for="motivo_cancelacion" class="form-label">Motivo de la Cancelación (requerido):</label>
                        <textarea name="motivo_cancelacion" id="motivo_cancelacion" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <hr class="my-4">
            <?php endif; ?>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>" class="btn btn-secondary">Volver</a>
                <button type="submit" class="btn btn-primary" <?php echo $order['estado'] === 'Cancelado' ? 'disabled' : ''; ?>>
                    <i class="bi bi-arrow-repeat me-2"></i>Actualizar Pedido
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const cancelarCheckbox = document.getElementById('cancelar-checkbox');
    if (cancelarCheckbox) {
        const motivoContainer = document.getElementById('motivo-container');
        const motivoTextarea = document.getElementById('motivo_cancelacion');
        const estadoSelect = document.getElementById('estado');

        cancelarCheckbox.addEventListener('change', function() {
            if (this.checked) {
                motivoContainer.style.display = 'block';
                motivoTextarea.required = true;
                estadoSelect.disabled = true;
                // Asignamos un valor especial al estado para que el backend lo ignore
                estadoSelect.name = 'estado_disabled'; 
            } else {
                motivoContainer.style.display = 'none';
                motivoTextarea.required = false;
                estadoSelect.disabled = false;
                estadoSelect.name = 'estado';
            }
        });
    }
</script>