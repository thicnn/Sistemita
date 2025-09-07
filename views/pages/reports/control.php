<h2 class="mb-4">Panel de Control</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/control" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
            </div>
            <div class="col-md-5">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Production Control -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header"><h5 class="mb-0">Control de Producción (Listos y Entregados)</h5></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($productionOrders)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No hay pedidos en producción para este período.</td></tr>
                            <?php else: foreach ($productionOrders as $order): ?>
                                <tr>
                                    <td><a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                                    <td><?php echo htmlspecialchars($order['nombre_cliente'] ?? 'Cliente no encontrado'); ?></td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($order['estado']); ?></span></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['fecha_creacion'])); ?></td>
                                    <td class="text-end">$<?php echo number_format($order['costo_total'], 2); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Counters and Payments -->
    <div class="col-lg-8">
        <div class="accordion" id="controlAccordion">
            <!-- Counter Management -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCounters">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCounters" aria-expanded="true" aria-controls="collapseCounters">
                        Gestión de Contadores
                    </button>
                </h2>
                <div id="collapseCounters" class="accordion-collapse collapse show" aria-labelledby="headingCounters" data-bs-parent="#controlAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Registrar Nuevo Contador</h6>
                                <form action="/sistemagestion/reports/store_counter" method="POST">
                                    <div class="mb-2"><select name="maquina" id="maquina-selector" class="form-select" required><option value="Bh-227">Bh-227</option><option value="C454e">C454e</option></select></div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><input type="date" name="fecha_inicio" class="form-control" required title="Fecha de Inicio"></div>
                                        <div class="col-6"><input type="date" name="fecha_fin" class="form-control" required title="Fecha de Fin" value="<?= date('Y-m-d'); ?>"></div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><input type="number" name="contador_bn" class="form-control" placeholder="Contador B&N" required></div>
                                        <div class="col-6"><input type="number" name="contador_color" id="contador-color" class="form-control" placeholder="Contador Color" style="display: none;"></div>
                                    </div>
                                    <div class="d-grid"><button type="submit" class="btn btn-secondary">Registrar</button></div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h6>Historial</h6>
                                <div class="table-responsive" style="max-height: 220px;">
                                    <table class="table table-sm">
                                        <thead><tr><th>Máquina</th><th>Período</th><th>B&N</th><th>Color</th></tr></thead>
                                        <tbody>
                                            <?php if (empty($counterHistory)): ?>
                                                <tr><td colspan="4" class="text-center text-muted">No hay registros.</td></tr>
                                            <?php else: foreach ($counterHistory as $c): ?>
                                                <tr><td><?= htmlspecialchars($c['maquina_nombre']); ?></td><td><?= date('d/m/y', strtotime($c['fecha_inicio'])) . ' - ' . date('d/m/y', strtotime($c['fecha_fin'])); ?></td><td><?= $c['contador_bn']; ?></td><td><?= $c['contador_color'] ?: ''; ?></td></tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Provider Payments -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingPayments">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePayments" aria-expanded="false" aria-controls="collapsePayments">
                        Pagos a Proveedor
                    </button>
                </h2>
                <div id="collapsePayments" class="accordion-collapse collapse" aria-labelledby="headingPayments" data-bs-parent="#controlAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Registrar Nuevo Pago</h6>
                                <form action="/sistemagestion/reports/store_provider_payment" method="POST">
                                    <div class="mb-2"><input type="date" name="fecha_pago" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
                                    <div class="mb-2"><input type="text" name="descripcion" class="form-control" placeholder="Descripción" required></div>
                                    <div class="mb-2"><input type="number" name="monto" class="form-control" step="0.01" placeholder="Monto" required></div>
                                    <div class="d-grid"><button type="submit" class="btn btn-secondary">Registrar</button></div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <h6>Historial</h6>
                                <div class="table-responsive" style="max-height: 220px;">
                                    <table class="table table-sm">
                                        <thead><tr><th>Fecha</th><th>Descripción</th><th>Monto</th></tr></thead>
                                        <tbody>
                                            <?php if (empty($providerPayments)): ?>
                                                <tr><td colspan="3" class="text-center text-muted">No hay pagos.</td></tr>
                                            <?php else: foreach ($providerPayments as $p): ?>
                                                <tr><td><?= date('d/m/Y', strtotime($p['fecha_pago'])); ?></td><td><?= htmlspecialchars($p['descripcion']); ?></td><td>$<?= number_format($p['monto'], 2); ?></td></tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
// Script for C454e color counter toggle
document.getElementById('maquina-selector').addEventListener('change', function() {
    const colorInput = document.getElementById('contador-color');
    colorInput.style.display = (this.value === 'C454e') ? 'block' : 'none';
    if (this.value !== 'C454e') {
        colorInput.value = '';
    }
}).dispatchEvent(new Event('change'));
</script>
