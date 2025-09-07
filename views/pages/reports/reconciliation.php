<h2 class="mb-4">Herramienta de Conciliación de Cuentas</h2>

<!-- Date Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/reconciliation" method="GET" class="row g-3 align-items-center justify-content-center">
            <div class="col-auto">
                <label for="date" class="form-label">Mostrar resumen de ventas para el día:</label>
            </div>
            <div class="col-auto">
                <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($selectedDate); ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Ver Día</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Daily Summary & Balances -->
    <div class="col-lg-5">
        <!-- Daily Sales -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Resumen del Día (<?php echo date('d/m/Y', strtotime($selectedDate)); ?>)</h5>
            </div>
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

        <!-- Account Balances -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Dinero por Depositar (Teórico)</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Efectivo a depositar
                        <span class="fs-5 fw-bold">$<?php echo number_format($accountBalances['efectivo'], 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        En cuenta bancaria
                        <span class="fs-5 fw-bold">$<?php echo number_format($accountBalances['banco'], 2); ?></span>
                    </li>
                </ul>
                 <p class="text-muted small mt-2">Este es el total de todos los pagos registrados menos los depósitos que has marcado en el historial.</p>
            </div>
        </div>
    </div>

    <!-- Register Deposit & History -->
    <div class="col-lg-7">
        <!-- Register Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Registrar Depósito o Movimiento</h5>
            </div>
            <div class="card-body">
                <form action="/sistemagestion/reports/store_deposit" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tipo_cuenta" class="form-label">Tipo de Cuenta</label>
                            <select id="tipo_cuenta" name="tipo_cuenta" class="form-select" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="banco">Cuenta Bancaria</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="monto" class="form-label">Monto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="monto" name="monto" class="form-control" required step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha del Movimiento</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="notas" class="form-label">Notas (Opcional)</label>
                            <input type="text" id="notas" name="notas" class="form-control">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success">Registrar Movimiento</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Table -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Historial de Movimientos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cuenta</th>
                                <th class="text-end">Monto</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($depositHistory)): ?>
                                <tr><td colspan="4" class="text-center text-muted p-4">No hay movimientos registrados.</td></tr>
                            <?php else: foreach ($depositHistory as $item): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($item['fecha'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $item['tipo_cuenta'] === 'efectivo' ? 'success' : 'primary'; ?>-subtle text-<?php echo $item['tipo_cuenta'] === 'efectivo' ? 'success' : 'primary'; ?>-emphasis">
                                            <?php echo ucfirst($item['tipo_cuenta']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">$<?php echo number_format($item['monto'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['notas']); ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
