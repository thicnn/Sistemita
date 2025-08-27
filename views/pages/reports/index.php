<?php
// --- Lógica de Fechas y Nombres ---
if (class_exists('IntlDateFormatter')) {
    $fmtMesAnio = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM \'de\' yyyy');
    $nombreMesSeleccionado = ucfirst($fmtMesAnio->format(strtotime($startDate)));
} else {
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $nombreMesSeleccionado = $meses[date('n', strtotime($startDate)) - 1] . ' de ' . date('Y', strtotime($startDate));
}
?>

<h2 class="mb-4">Panel de Reportes</h2>

<div class="card shadow-sm mb-4 animated-card">
    <div class="card-body">
        <form action="/sistemagestion/reports" method="GET" class="d-flex justify-content-center align-items-center flex-wrap gap-3">
            <label for="month-selector" class="form-label mb-0 fw-bold">Viendo Reportes para:</label>
            <input type="month" id="month-selector" name="month" class="form-control w-auto" value="<?php echo htmlspecialchars($selectedMonth); ?>">
            <button type="submit" class="btn btn-primary">Cambiar Mes</button>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.1s;">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Resumen General de <?php echo $nombreMesSeleccionado; ?></h5></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="row g-3">
                     <?php
                    $status_totals = [];
                    if (isset($statusCounts) && is_array($statusCounts)) { foreach ($statusCounts as $status) { $status_totals[$status['estado']] = $status['total']; } }
                    $all_statuses = ["Entregado", "Cancelado", "Listo para Retirar", "En Curso"];
                    foreach ($all_statuses as $s): $total = $status_totals[$s] ?? 0;
                    ?>
                    <div class="col-md-6">
                        <a href="/sistemagestion/reports/status/<?php echo urlencode($s); ?>" class="report-card-small h-100">
                            <span class="fs-2 fw-bold text-primary"><?php echo $total; ?></span>
                            <span class="text-muted"><?php echo $s; ?></span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-4">
                 <ul class="list-group h-100">
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span><i class="bi bi-printer me-2"></i>BH-227 (B&N)</span> <strong><?php echo $bh227_total_prod ?? 0; ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span><i class="bi bi-printer-fill me-2"></i>C454e (B&N)</span> <strong><?php echo $c454e_bn_prod ?? 0; ?></strong></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span><i class="bi bi-printer-fill text-primary me-2"></i>C454e (Color)</span> <strong><?php echo $c454e_color_prod ?? 0; ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.2s;">
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
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-secondary">Registrar Contador</button>
                    </div>
                </form>
                <h6 class="border-top pt-3">Historial</h6>
                <div class="table-responsive flex-grow-1" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead><tr><th><input type="checkbox" id="select-all-counters"></th><th>Máquina</th><th>Período</th><th>B&N</th><th>Color</th></tr></thead>
                        <tbody>
                            <?php if (empty($counterHistory)): ?>
                                <tr><td colspan="5" class="text-center text-muted p-3">No hay registros.</td></tr>
                            <?php else: foreach ($counterHistory as $c): ?>
                                <tr>
                                    <td><input type="checkbox" class="counter-checkbox" value="<?php echo $c['id']; ?>"></td>
                                    <td><?php echo htmlspecialchars($c['maquina_nombre']); ?></td>
                                    <td><?php echo date('d/m/y', strtotime($c['fecha_inicio'])) . ' - ' . date('d/m/y', strtotime($c['fecha_fin'])); ?></td>
                                    <td><?php echo $c['contador_bn']; ?></td>
                                    <td><?php echo $c['contador_color'] ?: ''; ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <button id="delete-selected-counters" class="btn btn-sm btn-outline-danger mt-2"><i class="bi bi-trash-fill"></i> Eliminar Seleccionados</button>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100 animated-card" style="animation-delay: 0.3s;">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-truck me-2"></i>Pagos a Proveedor</h5></div>
            <div class="card-body d-flex flex-column">
                <form action="/sistemagestion/reports/store_provider_payment" method="POST" class="mb-4">
                    <h6 class="mb-3">Registrar Nuevo Pago</h6>
                     <div class="row g-2">
                        <div class="col-md-5"><input type="date" name="fecha_pago" class="form-control" value="<?php echo date('Y-m-d'); ?>" required></div>
                        <div class="col-md-7"><input type="text" name="descripcion" class="form-control" placeholder="Descripción" required></div>
                        <div class="col-12"><input type="number" name="monto" class="form-control" step="0.01" placeholder="Monto" required></div>
                    </div>
                     <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-secondary">Registrar Pago</button>
                    </div>
                </form>
                <h6 class="border-top pt-3">Historial</h6>
                <div class="table-responsive flex-grow-1" style="max-height: 220px; overflow-y: auto;">
                    <table class="table table-sm">
                         <thead><tr><th><input type="checkbox" id="select-all-payments"></th><th>Fecha</th><th>Descripción</th><th>Monto</th></tr></thead>
                        <tbody>
                             <?php if (empty($providerPayments)): ?>
                                <tr><td colspan="4" class="text-center text-muted p-3">No hay pagos registrados.</td></tr>
                            <?php else: foreach ($providerPayments as $p): ?>
                                <tr>
                                    <td><input type="checkbox" class="payment-checkbox" value="<?php echo $p['id']; ?>"></td>
                                    <td><?php echo date('d/m/Y', strtotime($p['fecha_pago'])); ?></td>
                                    <td><?php echo htmlspecialchars($p['descripcion']); ?></td>
                                    <td>$<?php echo number_format($p['monto'], 2); ?></td>
                                </tr>
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
    @keyframes slideInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animated-card { opacity: 0; animation: slideInUp 0.6s ease-out forwards; }
    .report-card-small {
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        padding: 1rem; background-color: #f8f9fa; border: 1px solid #e9ecef;
        border-radius: 0.5rem; text-decoration: none; color: inherit; transition: all 0.2s ease-in-out; text-align: center;
    }
    .report-card-small:hover {
        transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1); border-color: var(--bs-primary);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lógica para el borrado de pagos
    document.getElementById('delete-selected-payments')?.addEventListener('click', async () => {
        const selectedIds = Array.from(document.querySelectorAll('.payment-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} pago(s)?`)) {
            await fetch('/sistemagestion/reports/delete_payments', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `ids[]=${selectedIds.join('&ids[]=')}`
            });
            location.reload();
        }
    });

    // Lógica para el borrado de contadores
    document.getElementById('delete-selected-counters')?.addEventListener('click', async () => {
        const selectedIds = Array.from(document.querySelectorAll('.counter-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} registro(s)?`)) {
            await fetch('/sistemagestion/reports/delete_counters', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `ids[]=${selectedIds.join('&ids[]=')}`
            });
            location.reload();
        }
    });

    // Lógica para "Seleccionar todo"
    document.getElementById('select-all-payments')?.addEventListener('change', e => {
        document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = e.target.checked);
    });
    document.getElementById('select-all-counters')?.addEventListener('change', e => {
        document.querySelectorAll('.counter-checkbox').forEach(cb => cb.checked = e.target.checked);
    });

    // Lógica para mostrar/ocultar campo de color en formulario de contadores
    document.getElementById('maquina-selector').addEventListener('change', function(){
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