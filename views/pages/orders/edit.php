<h2 class="mb-4">Editando Pedido #<?php echo $order['id']; ?></h2>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="/sistemagestion/orders/edit/<?php echo $order['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Cliente:</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Cambiar Estado:</label>
                        <select name="estado" id="estado" class="form-select" required <?php echo $order['estado'] === 'Cancelado' || $order['estado'] === 'Entregado' ? 'disabled' : ''; ?>>
                            <?php
                            $statusOrder = [
                                "Solicitud" => 1,
                                "Cotización" => 2,
                                "Confirmado" => 3,
                                "En curso" => 4,
                                "Listo para entregar" => 5,
                                "Entregado" => 6
                            ];
                            $estados = ["Solicitud", "Cotización", "Confirmado", "En curso", "Listo para entregar", "Entregado"];

                            if ($order['estado'] === 'Cancelado') {
                                echo "<option value='Cancelado' selected disabled>Cancelado</option>";
                            } else {
                                $currentStatusValue = $statusOrder[$order['estado']] ?? 0;

                                foreach ($estados as $estado) {
                                    $optionStatusValue = $statusOrder[$estado];
                                    $selected = ($order['estado'] == $estado) ? 'selected' : '';
                                    $disabled = ($optionStatusValue < $currentStatusValue) ? 'disabled' : '';
                                    echo "<option value='{$estado}' {$selected} {$disabled}>{$estado}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas del Pedido:</label>
                        <textarea name="notas" id="notas" rows="3" class="form-control" <?php echo $order['estado'] === 'Cancelado' ? 'disabled' : ''; ?>><?php echo htmlspecialchars($order['notas_internas'] ?? ''); ?></textarea>
                    </div>

                    <div id="pago-final-container" class="border-top pt-3 mt-3" style="display: none;">
                         <div class="mb-3">
                            <label class="form-label fw-bold">Método de Pago Final:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pago_final" id="pago_efectivo" value="Efectivo">
                                <label class="form-check-label" for="pago_efectivo">Efectivo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pago_final" id="pago_debito" value="Débito">
                                <label class="form-check-label" for="pago_debito">Débito</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="metodo_pago_final" id="pago_credito" value="Crédito">
                                <label class="form-check-label" for="pago_credito">Crédito</label>
                            </div>
                        </div>
                    </div>


                    <hr class="my-4">

                    <?php if ($order['estado'] !== 'Cancelado' && $order['estado'] !== 'Entregado'): ?>
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                                <i class="bi bi-x-circle-fill me-2"></i>Cancelar Pedido
                            </button>
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
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Actividad</h5>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                <?php if (empty($history)): ?>
                    <p class="text-muted text-center">No hay actividad registrada.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($history as $entry): ?>
                            <li class="list-group-item px-0">
                                <p class="mb-1"><?php echo htmlspecialchars($entry['descripcion']); ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($entry['nombre_usuario'] ?? 'Sistema'); ?>
                                    &middot;
                                    <i class="bi bi-calendar-event"></i> <?php echo date('d/m/Y H:i', strtotime($entry['fecha'])); ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Cancelación -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/sistemagestion/orders/edit/<?php echo $order['id']; ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Confirmar Cancelación de Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Estás a punto de cancelar el pedido #<?php echo $order['id']; ?>. Esta acción no se puede deshacer.</p>
                    <div class="mb-3">
                        <label for="motivo_cancelacion_modal" class="form-label">Por favor, escribe el motivo de la cancelación (requerido):</label>
                        <textarea name="motivo_cancelacion" id="motivo_cancelacion_modal" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const estadoSelect = document.getElementById('estado');
        const pagoFinalContainer = document.getElementById('pago-final-container');
        const pagoRadios = document.querySelectorAll('input[name="metodo_pago_final"]');

        function togglePagoFinal() {
            const esEntregado = estadoSelect.value === 'Entregado';
            const saldoPendiente = <?php echo $order['costo_total'] - array_sum(array_column($order['pagos'], 'monto')); ?>;

            if (esEntregado && saldoPendiente > 0.01) {
                pagoFinalContainer.style.display = 'block';
                pagoRadios.forEach(radio => radio.required = true);
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
            // Llamada inicial por si el estado ya es 'Entregado' al cargar
            togglePagoFinal();
        }

    });
</script>