<?php
// Función para badges de estado
function getStatusBadgeClass($status)
{
    $map = [
        'Entregado' => 'bg-success-subtle text-success-emphasis',
        'Listo para Retirar' => 'bg-info-subtle text-info-emphasis',
        'En Curso' => 'bg-primary-subtle text-primary-emphasis',
        'Confirmado' => 'bg-warning-subtle text-warning-emphasis',
        'Cancelado' => 'bg-danger-subtle text-danger-emphasis',
        'Solicitud' => 'bg-secondary-subtle text-secondary-emphasis',
        'Cotización' => 'bg-light text-dark border'
    ];
    return $map[$status] ?? 'bg-dark';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="mb-0">Ficha del Cliente</h2>
    <a href="/sistemagestion/clients" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Volver al Listado</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm animated-card mb-4">
            <div class="card-body text-center">
                <div class="avatar-lg mx-auto mb-3">
                    <?php echo strtoupper(substr($client['nombre'], 0, 1)); ?>
                </div>
                <h4 class="card-title mb-1"><?php echo htmlspecialchars($client['nombre']); ?></h4>
                <p class="text-muted">ID de Cliente: #<?php echo $client['id']; ?></p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-telephone-fill me-2 text-muted"></i><?php echo htmlspecialchars($client['telefono'] ?: 'No registrado'); ?></li>
                <li class="list-group-item"><i class="bi bi-envelope-fill me-2 text-muted"></i><?php echo htmlspecialchars($client['email'] ?: 'No registrado'); ?></li>
            </ul>
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Estadísticas Globales</h6>
                <div class="row text-center">
                    <div class="col">
                        <div class="fs-4 fw-bold"><?php echo $client['total_pedidos']; ?></div>
                        <small class="text-muted">Pedidos</small>
                    </div>
                    <div class="col">
                        <div class="fs-4 fw-bold">$<?php echo number_format($client['total_gastado'], 2); ?></div>
                        <small class="text-muted">Ventas</small>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($descuentoInfo) && is_array($descuentoInfo)): ?>
        <div class="card shadow-sm animated-card" style="animation-delay: 0.1s;">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="bi bi-star-fill me-2 text-warning"></i>
                <h5 class="mb-0">Programa de Fidelidad (Mensual)</h5>
            </div>
            <div class="card-body">
                <?php if ($descuentoInfo['disponible']): ?>
                    <div class="text-center text-success">
                        <div class="fs-2 fw-bold">¡Descuento Disponible!</div>
                        <p class="mb-0">Este cliente tiene un descuento de <strong>$<?php echo number_format($descuentoInfo['monto_descuento'], 2); ?></strong> para usar este mes.</p>
                    </div>
                <?php elseif ($descuentoInfo['usado']): ?>
                    <div class="text-center text-muted">
                        <div class="fs-2 fw-bold"><i class="bi bi-check-circle-fill"></i></div>
                        <p class="mb-0">El descuento de este mes ya fue utilizado.</p>
                    </div>
                <?php else: ?>
                    <div>
                        <p class="mb-2">Progreso para alcanzar el descuento:</p>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $descuentoInfo['progreso_pct']; ?>%;" aria-valuenow="<?php echo $descuentoInfo['progreso_pct']; ?>" aria-valuemin="0" aria-valuemax="100">
                                <?php echo number_format($descuentoInfo['gasto_actual'], 2); ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">$0</small>
                            <small class="text-muted">$<?php echo $descuentoInfo['meta']; ?></small>
                        </div>
                        <p class="text-center mt-3">
                            Faltan <strong>$<?php echo number_format($descuentoInfo['restante'], 2); ?></strong> para obtener el beneficio.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm animated-card" style="animation-delay: 0.2s;">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Historial de Pedidos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($client['pedidos'])): ?>
                    <p class="text-center text-muted p-4">Este cliente aún no tiene pedidos.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th class="text-end">Monto</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($client['pedidos'] as $pedido): ?>
                                    <tr>
                                        <td>#<?php echo $pedido['id']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($pedido['fecha_creacion'])); ?></td>
                                        <td><span class="badge rounded-pill <?php echo getStatusBadgeClass($pedido['estado']); ?>"><?php echo $pedido['estado']; ?></span></td>
                                        <td class="text-end">$<?php echo number_format($pedido['costo_total'], 2); ?></td>
                                        <td class="text-end"><a href="/sistemagestion/orders/show/<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-lg {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background-color: var(--bs-primary);
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 3rem;
        font-weight: 300;
    }
</style>