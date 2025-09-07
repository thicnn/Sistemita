<?php
$mesesEnEspanol = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$numeroMes = date('m', strtotime($selectedMonth));
$anio = date('Y', strtotime($selectedMonth));
$nombreMesSeleccionado = $mesesEnEspanol[$numeroMes] . ' de ' . $anio;
?>

<h2 class="mb-4">Panel de Reportes</h2>

<!-- Main Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports" method="GET" class="d-flex justify-content-center align-items-center flex-wrap gap-3">
            <label for="month-selector" class="form-label mb-0 fw-bold">Filtro General por Mes:</label>
            <input type="month" id="month-selector" name="month" class="form-control w-auto" value="<?php echo htmlspecialchars($selectedMonth); ?>">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- General Sales Chart -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Ventas Generales de <?php echo $nombreMesSeleccionado; ?></h5></div>
            <div class="card-body"><canvas id="salesOverTimeChart" style="min-height: 250px;"></canvas></div>
        </div>
    </div>
    <!-- Order Status Distribution -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Distribución de Estados</h5></div>
            <div class="card-body d-flex justify-content-center align-items-center"><div style="position: relative; height:200px; width:200px"><canvas id="ordersChart"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Top 10 Profitable Products -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Top 10 Productos Rentables de <?php echo $nombreMesSeleccionado; ?></h5></div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-striped table-hover">
                    <thead><tr><th>Producto</th><th class="text-center">Unidades</th><th class="text-end">Ingresos</th></tr></thead>
                    <tbody>
                        <?php if (empty($topProducts)): ?>
                            <tr><td colspan="3" class="text-center text-muted p-4">No hay datos de ventas para este mes.</td></tr>
                        <?php else: foreach ($topProducts as $product): ?>
                            <tr><td><?php echo htmlspecialchars($product['descripcion']); ?></td><td class="text-center"><?php echo $product['unidades_vendidas']; ?></td><td class="text-end fw-bold">$<?php echo number_format($product['ingresos_generados'], 2); ?></td></tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
    <!-- Production Counters -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bi bi-printer-fill me-2"></i>Contadores de Producción</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless my-auto">
                    <tbody>
                        <tr><td><i class="bi bi-printer me-2"></i><strong>Bh-227</strong> (B&N)</td><td class="text-end fs-5 fw-bold"><?php echo number_format($printerCounters['bh227_bw'], 0); ?></td></tr>
                        <tr><td><i class="bi bi-printer me-2"></i><strong>C454e</strong> (B&N)</td><td class="text-end fs-5 fw-bold"><?php echo number_format($printerCounters['c454e_bw'], 0); ?></td></tr>
                        <tr><td><i class="bi bi-printer-fill me-2" style="color: #0d6efd;"></i><strong>C454e</strong> (Color)</td><td class="text-end fs-5 fw-bold"><?php echo number_format($printerCounters['c454e_color'], 0); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Supplier Payment History -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-truck me-2"></i>Historial de Pagos a Proveedores</h5></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead><tr><th>Fecha</th><th>Descripción</th><th>Monto</th></tr></thead>
                        <tbody>
                            <?php if (empty($providerPayments)): ?>
                                <tr><td colspan="3" class="text-center text-muted p-3">No hay pagos registrados.</td></tr>
                            <?php else: foreach ($providerPayments as $p): ?>
                                <tr><td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td><td><?php echo htmlspecialchars($p['descripcion']); ?></td><td>$<?php echo number_format($p['monto'], 2); ?></td></tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Counter Registration History -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-calculator-fill me-2"></i>Historial de Registro de Contadores</h5></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead><tr><th>Máquina</th><th>Período</th><th>B&N</th><th>Color</th></tr></thead>
                        <tbody>
                            <?php if (empty($counterHistory)): ?>
                                <tr><td colspan="4" class="text-center text-muted p-3">No hay registros.</td></tr>
                            <?php else: foreach ($counterHistory as $c): ?>
                                <tr><td><?php echo htmlspecialchars($c['maquina_nombre']); ?></td><td><?php echo date('d/m/y', strtotime($c['fecha_inicio'])) . ' - ' . date('d/m/y', strtotime($c['fecha_fin'])); ?></td><td><?php echo $c['contador_bn']; ?></td><td><?php echo $c['contador_color'] ?: ''; ?></td></tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-5">

<h3 class="mb-4 text-center">Submenús de Reportes</h3>

<div class="row g-4">
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/sales" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-cash-coin fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Ventas</h5>
                    <p class="card-text">Detalles de ventas, ganancias y pérdidas.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/weekly_production" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-week fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Producción Semanal</h5>
                    <p class="card-text">Análisis de producción y ventas por semana.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/orders" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-box-seam fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Pedidos</h5>
                    <p class="card-text">Detalles de pedidos, estados y filtros.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/control" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-gear-wide-connected fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Control</h5>
                    <p class="card-text">Control de producción y registros.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/products" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-tag fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Productos</h5>
                    <p class="card-text">Análisis de ventas por producto.</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-6">
        <a href="/sistemagestion/reports/clients" class="report-card-link">
            <div class="card report-card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h5 class="card-title mt-3">Clientes</h5>
                    <p class="card-text">Tendencias y análisis de clientes.</p>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.report-card-link {
    text-decoration: none;
    color: inherit;
}
.report-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Order Status Distribution Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        const chartLabels = <?php echo json_encode($chartLabels ?? []); ?>;
        const chartData = <?php echo json_encode($chartData ?? []); ?>;

        if (chartLabels.length > 0) {
            new Chart(ordersCtx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pedidos',
                        data: chartData,
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.8)', 'rgba(220, 53, 69, 0.8)',
                            'rgba(13, 202, 240, 0.8)', 'rgba(13, 110, 253, 0.8)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        } else {
            const canvasCtx = ordersCtx.getContext('2d');
            canvasCtx.textAlign = 'center';
            canvasCtx.textBaseline = 'middle';
            canvasCtx.fillStyle = '#6c757d';
            canvasCtx.fillText("No hay datos", ordersCtx.width / 2, ordersCtx.height / 2);
        }
    }

    // General Sales Chart
    const salesCtx = document.getElementById('salesOverTimeChart');
    if (salesCtx) {
        const salesData = <?php echo json_encode($salesOverTime ?? []); ?>;
        const salesLabels = salesData.map(d => {
            const date = new Date(d.mes + '-02');
            return date.toLocaleString('es-ES', { month: 'long', year: 'numeric' });
        });
        const salesValues = salesData.map(d => d.total_ventas);

        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Ingresos Mensuales',
                    data: salesValues,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>
