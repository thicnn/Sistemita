<?php
// --- Lógica de Fechas y Nombres (VERSIÓN CORREGIDA Y SIMPLIFICADA) ---
$mesesEnEspanol = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$numeroMes = date('m', strtotime($startDate));
$anio = date('Y', strtotime($startDate));
$nombreMesSeleccionado = $mesesEnEspanol[$numeroMes] . ' de ' . $anio;
?>

<h2 class="mb-4">Panel de Reportes</h2>

<!-- Main Filter -->
<div class="card shadow-sm mb-4 animated-card">
    <div class="card-body">
        <form action="/sistemagestion/reports" method="GET" class="d-flex justify-content-center align-items-center flex-wrap gap-3">
            <label for="month-selector" class="form-label mb-0 fw-bold">Filtro General por Mes (Top Productos, Pérdidas):</label>
            <input type="month" id="month-selector" name="month" class="form-control w-auto" value="<?php echo htmlspecialchars($selectedMonth); ?>">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
    </div>
</div>

<!-- Row 1: KPIs -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100 animated-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Ganancia por Período</h5>
                <form action="/sistemagestion/reports" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <input type="date" name="profit_start" class="form-control form-control-sm" value="<?php echo htmlspecialchars($profitStartDate); ?>" title="Fecha de inicio">
                    <span class="text-muted">-</span>
                    <input type="date" name="profit_end" class="form-control form-control-sm" value="<?php echo htmlspecialchars($profitEndDate); ?>" title="Fecha de fin">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Calcular</button>
                </form>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">Ingresos Totales (Venta)<span class="badge bg-success-subtle text-success-emphasis fs-6">$<?php echo number_format($profitReport['total_revenue'], 2); ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">Costos Totales<span class="badge bg-warning-subtle text-warning-emphasis fs-6">$<?php echo number_format($profitReport['total_cost'], 2); ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">Ganancia Neta<span class="badge bg-primary fs-5">$<?php echo number_format($profitReport['total_profit'], 2); ?></span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 animated-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="bi bi-printer-fill me-2"></i>Contadores de Producción</h5>
                 <form action="/sistemagestion/reports" method="GET" class="d-flex align-items-center gap-2">
                    <input type="month" name="counters_month" class="form-control form-control-sm" value="<?php echo htmlspecialchars($countersMonth); ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Calcular</button>
                </form>
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

<!-- Row 2: Time Series Graphs -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm animated-card" style="animation-delay: 0.1s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Evolución de Ventas (Últimos 6 Meses)</h5></div>
            <div class="card-body"><canvas id="salesOverTimeChart" style="min-height: 250px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm animated-card" style="animation-delay: 0.2s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Evolución de Ganancia (Últimos 12 Meses)</h5></div>
            <div class="card-body"><canvas id="profitOverTimeChart" style="min-height: 250px;"></canvas></div>
        </div>
    </div>
</div>

<!-- Row 3: More Graphs -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm animated-card" style="animation-delay: 0.3s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Pedidos por Mes (Últimos 6 Meses)</h5></div>
            <div class="card-body"><canvas id="ordersOverTimeChart" style="min-height: 250px;"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.3s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Distribución de Estados</h5></div>
            <div class="card-body d-flex justify-content-center align-items-center"><div style="position: relative; height:200px; width:200px"><canvas id="ordersChart"></canvas></div></div>
        </div>
    </div>
</div>

<!-- Row 4: Tables and Details -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.2s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-tags-fill me-2"></i>Top 10 Productos Más Rentables de <?php echo $nombreMesSeleccionado; ?></h5></div>
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
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 animated-card">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-safe-fill me-2"></i>Distribución de Ventas de <?php echo $nombreMesSeleccionado; ?></h5></div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Efectivo en Caja
                        <span class="badge bg-success-subtle text-success-emphasis fs-6">$<?php echo number_format($salesDistribution['efectivo'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Acreditado en Banco
                        <span class="badge bg-primary-subtle text-primary-emphasis fs-6">$<?php echo number_format($salesDistribution['debitado'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pendiente de Acreditación
                        <span class="badge bg-warning-subtle text-warning-emphasis fs-6">$<?php echo number_format($salesDistribution['pendiente'], 2); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card shadow-sm h-100 animated-card bg-danger-subtle text-danger-emphasis mt-4">
            <div class="card-body text-center">
                <h5 class="card-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Pérdidas por Errores en <?php echo $nombreMesSeleccionado; ?></h5>
                <p class="card-text fs-2 fw-bold">$<?php echo number_format($totalLosses, 2); ?></p>
                <a href="/sistemagestion/errors/create" class="btn btn-sm btn-outline-danger">Registrar Nuevo Error</a>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Management Widgets -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.4s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-calculator-fill me-2"></i>Gestión de Contadores</h5></div>
            <div class="card-body d-flex flex-column">
                <form action="/sistemagestion/reports/store_counter" method="POST" class="mb-4">
                    <h6 class="mb-3">Registrar Nuevo Contador</h6>
                    <div class="row g-2">
                        <div class="col-12"><select name="maquina" id="maquina-selector" class="form-select" required><option value="Bh-227">Bh-227</option><option value="C454e">C454e</option></select></div>
                        <div class="col-6"><input type="date" name="fecha_inicio" class="form-control" required title="Fecha de Inicio"></div>
                        <div class="col-6"><input type="date" name="fecha_fin" class="form-control" required title="Fecha de Fin" value="<?php echo date('Y-m-d'); ?>"></div>
                        <div class="col-6"><input type="number" name="contador_bn" class="form-control" placeholder="Contador B&N" required></div>
                        <div class="col-6"><input type="number" name="contador_color" id="contador-color" class="form-control" placeholder="Contador Color" style="display: none;"></div>
                    </div>
                    <div class="d-grid mt-2"><button type="submit" class="btn btn-secondary">Registrar Contador</button></div>
                </form>
                <h6 class="border-top pt-3">Historial</h6>
                <div class="table-responsive flex-grow-1" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead><tr><th><input type="checkbox" id="select-all-counters"></th><th>Máquina</th><th>Período</th><th>B&N</th><th>Color</th></tr></thead>
                        <tbody>
                            <?php if (empty($counterHistory)): ?>
                                <tr><td colspan="5" class="text-center text-muted p-3">No hay registros.</td></tr>
                            <?php else: foreach ($counterHistory as $c): ?>
                                <tr><td><input type="checkbox" class="counter-checkbox" value="<?php echo $c['id']; ?>"></td><td><?php echo htmlspecialchars($c['maquina_nombre']); ?></td><td><?php echo date('d/m/y', strtotime($c['fecha_inicio'])) . ' - ' . date('d/m/y', strtotime($c['fecha_fin'])); ?></td><td><?php echo $c['contador_bn']; ?></td><td><?php echo $c['contador_color'] ?: ''; ?></td></tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <button id="delete-selected-counters" class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-trash-fill"></i> Eliminar Seleccionados</button>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.5s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-truck me-2"></i>Pagos a Proveedor</h5></div>
            <div class="card-body d-flex flex-column">
                <form action="/sistemagestion/reports/store_provider_payment" method="POST" class="mb-4">
                    <h6 class="mb-3">Registrar Nuevo Pago</h6>
                    <div class="row g-2">
                        <div class="col-md-5"><input type="date" name="fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                        <div class="col-md-7"><input type="text" name="descripcion" class="form-control" placeholder="Descripción" required></div>
                        <div class="col-12"><input type="number" name="monto" class="form-control" step="0.01" placeholder="Monto" required></div>
                    </div>
                    <div class="d-grid mt-2"><button type="submit" class="btn btn-secondary">Registrar Pago</button></div>
                </form>
                <h6 class="border-top pt-3">Historial</h6>
                <div class="table-responsive flex-grow-1" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead><tr><th><input type="checkbox" id="select-all-payments"></th><th>Fecha</th><th>Descripción</th><th>Monto</th></tr></thead>
                        <tbody>
                            <?php if (empty($providerPayments)): ?>
                                <tr><td colspan="4" class="text-center text-muted p-3">No hay pagos registrados.</td></tr>
                            <?php else: foreach ($providerPayments as $p): ?>
                                <tr><td><input type="checkbox" class="payment-checkbox" value="<?php echo $p['id']; ?>"></td><td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td><td><?php echo htmlspecialchars($p['descripcion']); ?></td><td>$<?php echo number_format($p['monto'], 2); ?></td></tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <button id="delete-selected-payments" class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-trash-fill"></i> Eliminar Seleccionados</button>
            </div>
        </div>
    </div>
</div>

<style>
    .report-card-small {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem;
        background-color: var(--bs-body-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 0.5rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease-in-out;
        text-align: center;
    }

    .report-card-small:hover {
        background-color: var(--bs-tertiary-bg);
        border: 1px solid var(--bs-border-color);
        transform: translateY(-5px);
        box-shadow: var(--bs-box-shadow-sm);
        border-color: var(--bs-primary);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- PREPARACIÓN DE DATOS PARA GRÁFICOS ---
        // Se declaran aquí para que estén disponibles para todos los gráficos que los necesiten.
        const salesData = <?php echo json_encode($salesOverTime ?? []); ?>;
        const salesLabels = salesData.map(d => {
            const date = new Date(d.mes + '-02'); // Use second day to avoid timezone issues
            return date.toLocaleString('es-ES', {
                month: 'long',
                year: 'numeric'
            });
        });
        const salesValues = salesData.map(d => d.total_ventas);
        const ordersValues = salesData.map(d => d.total_pedidos);


        // Lógica para el gráfico de dona (Estados)
        const ctx = document.getElementById('ordersChart');
        if (ctx) {
            const chartLabels = <?php echo json_encode($chartLabels ?? []); ?>;
            const chartData = <?php echo json_encode($chartData ?? []); ?>;

            if (chartLabels.length > 0) {
                new Chart(ctx, {
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
                            borderColor: getComputedStyle(document.documentElement).getPropertyValue('--bs-body-tertiary'),
                            borderWidth: 4,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } else {
                const canvasCtx = ctx.getContext('2d');
                canvasCtx.textAlign = 'center';
                canvasCtx.textBaseline = 'middle';
                canvasCtx.fillStyle = '#6c757d';
                canvasCtx.fillText("No hay datos para mostrar en el gráfico.", ctx.width / 2, ctx.height / 2);
            }
        }

        // --- LÓGICA PARA EL NUEVO GRÁFICO DE LÍNEAS (VENTAS) ---
        const salesCtx = document.getElementById('salesOverTimeChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Ingresos Mensuales',
                        data: salesValues,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        yAxisID: 'y'
                    }, {
                        label: 'Pedidos Mensuales',
                        data: ordersValues,
                        backgroundColor: 'rgba(25, 135, 84, 0.6)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 2,
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            type: 'linear',
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Ingresos ($)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            type: 'linear',
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Cantidad de Pedidos'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }

        // --- LÓGICA PARA GRÁFICO DE GANANCIAS ---
        const profitCtx = document.getElementById('profitOverTimeChart');
        if (profitCtx) {
            const profitData = <?php echo json_encode($profitOverTime ?? []); ?>;
            const profitLabels = profitData.map(d => {
                const date = new Date(d.month + '-02');
                return date.toLocaleString('es-ES', { month: 'short', year: 'numeric' });
            });
            const profitValues = profitData.map(d => d.revenue - d.cost);

            new Chart(profitCtx, {
                type: 'bar',
                data: {
                    labels: profitLabels,
                    datasets: [{
                        label: 'Ganancia Neta Mensual',
                        data: profitValues,
                        backgroundColor: 'rgba(25, 135, 84, 0.6)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Ganancia ($)' }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // --- LÓGICA PARA GRÁFICO DE PEDIDOS ---
        const ordersCtx = document.getElementById('ordersOverTimeChart');
        if (ordersCtx && typeof salesData !== 'undefined') {
            new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: salesLabels, // Reutilizamos los labels del gráfico de ventas
                    datasets: [{
                        label: 'Pedidos Mensuales',
                        data: ordersValues, // Reutilizamos los datos de pedidos
                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            title: { display: true, text: 'Cantidad de Pedidos' }
                        }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Lógica para formularios y borrado
        document.getElementById('delete-selected-payments')?.addEventListener('click', async () => {
            const selectedIds = Array.from(document.querySelectorAll('.payment-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} pago(s)?`)) {
                await fetch('/sistemagestion/reports/delete_payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `ids[]=${selectedIds.join('&ids[]=')}`
                });
                location.reload();
            }
        });

        document.getElementById('delete-selected-counters')?.addEventListener('click', async () => {
            const selectedIds = Array.from(document.querySelectorAll('.counter-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} registro(s)?`)) {
                await fetch('/sistemagestion/reports/delete_counters', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `ids[]=${selectedIds.join('&ids[]=')}`
                });
                location.reload();
            }
        });

        document.getElementById('select-all-payments')?.addEventListener('change', e => {
            document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = e.target.checked);
        });

        document.getElementById('select-all-counters')?.addEventListener('change', e => {
            document.querySelectorAll('.counter-checkbox').forEach(cb => cb.checked = e.target.checked);
        });

        document.getElementById('maquina-selector').addEventListener('change', function() {
            const colorInput = document.getElementById('contador-color');
            if (this.value === 'Bh-227') {
                colorInput.style.display = 'none';
                colorInput.value = '';
            } else {
                colorInput.style.display = 'block';
            }
        }).dispatchEvent(new Event('change'));
    });
</script>