<?php
// Calculamos los totales de pagos
$totalPagado = array_sum(array_column($order['pagos'], 'monto'));
$saldoPendiente = $order['costo_total'] - $totalPagado;
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="mb-0">Detalles del Pedido #<?php echo $order['id']; ?></h2>
    <div>
        <a href="/sistemagestion/orders" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver</a>
        <a href="/sistemagestion/orders/edit/<?php echo $order['id']; ?>" class="btn btn-primary"><i class="bi bi-pencil-fill me-2"></i>Editar</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4 animated-card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Ítems del Pedido</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Descripción</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-end">Descuento</th>
                                <th class="text-end fw-bold">Total Ítem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal_neto_items = 0;
                            $subtotal_bruto_items = 0;
                            foreach ($order['items'] as $item):
                                // Con el nuevo schema, los datos ya vienen unidos y son más simples.
                                $precio_unitario = (float)$item['precio_unitario'];
                                $subtotal_original_item = $precio_unitario * $item['cantidad'];
                                $descuento_aplicado_item = $subtotal_original_item - $item['subtotal'];

                                $subtotal_bruto_items += $subtotal_original_item;
                                $subtotal_neto_items += $item['subtotal'];
                            ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($item['descripcion']); ?>
                                        <small class="d-block text-muted">
                                            <?php echo $item['cantidad']; ?> x $<?php echo number_format($precio_unitario, 2); ?> c/u
                                        </small>
                                    </td>
                                    <td class="text-end">$<?php echo number_format($subtotal_original_item, 2); ?></td>
                                    <td class="text-end text-danger">
                                        <?php if ($descuento_aplicado_item > 0.01): ?>
                                            -$<?php echo number_format($descuento_aplicado_item, 2); ?>
                                        <?php else: ?>
                                            $0.00
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm animated-card" style="animation-delay: 0.1s;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Pagos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($order['pagos'])): ?>
                    <p class="text-muted">No se han registrado pagos para este pedido.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($order['pagos'] as $pago): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="bi bi-calendar-check me-2"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($pago['fecha_pago'])); ?>
                                    <small class="text-muted ms-2">(<?php echo htmlspecialchars($pago['metodo_pago']); ?>)</small>
                                </div>
                                <span class="fw-bold">$<?php echo number_format($pago['monto'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($saldoPendiente > 0.009 && $order['estado'] !== 'Cancelado'): ?>
                    <hr>
                    <form action="/sistemagestion/orders/add_payment/<?php echo $order['id']; ?>" method="POST">
                        <h6 class="mb-3">Registrar Nuevo Pago</h6>
                        <div class="row g-2">
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="monto" class="form-control" step="0.01" max="<?php echo $saldoPendiente; ?>" placeholder="Monto" required>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <select name="metodo_pago" class="form-select" required>
                                    <option value="Efectivo" selected>Efectivo</option>
                                    <option value="Débito">Débito</option>
                                    <option value="Crédito">Crédito</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success w-100">OK</button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm animated-card" style="animation-delay: 0.2s;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Información General</h5>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?></p>
                <p><strong>Estado:</strong> <span class="badge rounded-pill <?php echo getStatusBadgeClass($order['estado']); ?>"><?php echo htmlspecialchars($order['estado']); ?></span></p>
                <p><strong>Fecha de Creación:</strong> <?php echo date('d/m/Y H:i', strtotime($order['fecha_creacion'])); ?></p>
                <p class="mb-0"><strong>Notas:</strong></p>
                <p class="text-muted border-start border-2 ps-2"><?php echo nl2br(htmlspecialchars($order['notas_internas'] ?: 'Sin notas.')); ?></p>

                <?php if ($order['estado'] === 'Cancelado' && !empty($order['motivo_cancelacion'])): ?>
                    <div class="alert alert-danger mt-3">
                        <strong>Motivo de Cancelación:</strong> <?php echo htmlspecialchars($order['motivo_cancelacion']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm mt-4 animated-card" style="animation-delay: 0.3s;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Resumen Financiero</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Subtotal (con desc. items): <span>$<?php echo number_format($subtotal_neto_items, 2); ?></span>
                </li>
                <?php
                    // Recalculamos el descuento total que se aplicó sobre el subtotal de items
                    $descuento_total_aplicado = $subtotal_neto_items - $order['costo_total'];
                    if ($descuento_total_aplicado > 0.01):
                        $porcentaje_descuento = ($subtotal_neto_items > 0) ? ($descuento_total_aplicado / $subtotal_neto_items) * 100 : 0;
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center text-danger">
                        <span>
                            Descuento Adicional
                            <small class="d-block text-muted">(<?php echo number_format($porcentaje_descuento, 1); ?>%)</small>
                        </span>
                        <span>-$<?php echo number_format($descuento_total_aplicado, 2); ?></span>
                    </li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Costo Total: <span class="fs-5 fw-bold">$<?php echo number_format($order['costo_total'], 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                    Total Pagado: <span class="fs-5 fw-bold">$<?php echo number_format($totalPagado, 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center text-danger fw-bold">
                    Saldo Pendiente: <span class="fs-5">$<?php echo number_format($saldoPendiente, 2); ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>