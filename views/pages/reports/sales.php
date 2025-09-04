<h2 class="mb-4">Detalle de Ventas</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/sales" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
            </div>
            <div class="col-md-3">
                <label for="client_id" class="form-label">Cliente</label>
                <select id="client_id" name="client_id" class="form-select">
                    <option value="">Todos los clientes</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id']; ?>" <?php echo (isset($clientId) && $clientId == $client['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($client['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="order_by" class="form-label">Ordenar por</label>
                <select id="order_by" name="order_by" class="form-select">
                    <option value="p.fecha_creacion DESC" <?php echo ($orderBy === 'p.fecha_creacion DESC') ? 'selected' : ''; ?>>Fecha (Más recientes)</option>
                    <option value="p.fecha_creacion ASC" <?php echo ($orderBy === 'p.fecha_creacion ASC') ? 'selected' : ''; ?>>Fecha (Más antiguos)</option>
                    <option value="total_final DESC" <?php echo ($orderBy === 'total_final DESC') ? 'selected' : ''; ?>>Total (Mayor a menor)</option>
                    <option value="total_final ASC" <?php echo ($orderBy === 'total_final ASC') ? 'selected' : ''; ?>>Total (Menor a mayor)</option>
                    <option value="ganancia DESC" <?php echo ($orderBy === 'ganancia DESC') ? 'selected' : ''; ?>>Ganancia (Mayor a menor)</option>
                    <option value="ganancia ASC" <?php echo ($orderBy === 'ganancia ASC') ? 'selected' : ''; ?>>Ganancia (Menor a mayor)</option>
                </select>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Evolución de Ventas y Ganancias</h5></div>
            <div class="card-body"><canvas id="evolutionChart" style="min-height: 250px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="card shadow-sm bg-danger-subtle text-danger-emphasis">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pérdidas por Errores</h5>
                        <p class="card-text fs-2 fw-bold">$<?php echo number_format($losses, 2); ?></p>
                        <a href="/sistemagestion/errors/create" class="btn btn-sm btn-outline-danger">Registrar Nuevo Error</a>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Distribución de Ventas</h5></div>
                    <div class="card-body">
                         <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Efectivo en Caja
                                <span class="badge bg-success-subtle text-success-emphasis fs-6">$<?= number_format($salesDistribution['efectivo'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Acreditado en Banco
                                <span class="badge bg-primary-subtle text-primary-emphasis fs-6">$<?= number_format($salesDistribution['debitado'], 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pendiente de Acreditación
                                <span class="badge bg-warning-subtle text-warning-emphasis fs-6">$<?= number_format($salesDistribution['pendiente'], 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Costos Totales</h5>
                        <p class="card-text fs-2 fw-bold">$<?php echo number_format($profitReport['total_cost'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th class="text-end">Total Final</th>
                        <th class="text-end">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No se encontraron ventas para los filtros seleccionados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><a href="/sistemagestion/orders/show/<?php echo $sale['pedido_id']; ?>">#<?php echo $sale['pedido_id']; ?></a></td>
                                <td><?php echo htmlspecialchars($sale['cliente_nombre'] ?? 'Cliente no encontrado'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($sale['fecha_creacion'])); ?></td>
                                <td class="text-end fw-bold">$<?php echo number_format($sale['total_final'], 2); ?></td>
                                <td class="text-end text-success fw-bold">$<?php echo number_format($sale['ganancia'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        const evolutionData = <?php echo json_encode($evolution ?? []); ?>;
        const evolutionLabels = evolutionData.map(d => new Date(d.dia).toLocaleDateString('es-ES'));
        const salesValues = evolutionData.map(d => d.total_ventas);
        const profitValues = evolutionData.map(d => d.total_ganancia);

        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: evolutionLabels,
                datasets: [
                    {
                        label: 'Ventas',
                        data: salesValues,
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Ganancia',
                        data: profitValues,
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        fill: true,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        type: 'linear',
                        position: 'left',
                        title: { display: true, text: 'Monto ($)' }
                    }
                }
            }
        });
    }
});
</script>
