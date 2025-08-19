<?php
// Lógica para mostrar fechas en español sin usar funciones obsoletas
$fmtMesAnio = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM \'de\' yyyy');
$fmtMes = new IntlDateFormatter('es_ES', IntlDateFormatter::LONG, IntlDateFormatter::NONE, null, null, 'MMMM');

$nombreMesSeleccionado = ucfirst($fmtMesAnio->format(strtotime($startDate)));
$nombreProximoMes = ucfirst($fmtMes->format(strtotime('+1 month')));
?>

<h2>Panel de Reportes</h2>

<div class="report-section">
    <form action="/sistemagestion/reports" method="GET" class="inline-form main-filter">
        <label for="month-selector">Viendo Reporte Histórico para:</label>
        <input type="month" id="month-selector" name="month" value="<?php echo htmlspecialchars($selectedMonth); ?>">
        <button type="submit">Cambiar Mes</button>
    </form>
</div>

<div class="report-section">
    <h3>Resumen para <?php echo $nombreMesSeleccionado; ?></h3>
    <div class="card-grid status-grid">
        <?php
        $status_totals = [];
        if (isset($statusCounts) && is_array($statusCounts)) {
            foreach ($statusCounts as $status) {
                $status_totals[$status['estado']] = $status['total'];
            }
        }
        $all_statuses = ["Entregado", "Cancelado", "Listo para Retirar", "En Curso", "Confirmado", "Cotización", "Solicitud"];
        foreach ($all_statuses as $s) {
            $total = $status_totals[$s] ?? 0;
            echo "<a href='/sistemagestion/reports/status/" . urlencode($s) . "' class='report-card'><h4>{$s}</h4><p class='data-number'>{$total}</p></a>";
        }
        ?>
    </div>
</div>

<div class="report-section">
    <h3>Producción para <?php echo $nombreMesSeleccionado; ?></h3>
    <div class="card-grid">
        <div class="report-card"><h4>BH-227 (Producción del Mes)</h4><p class="data-number"><?php echo $bh227_total_prod ?? 0; ?> / 2000</p></div>
        <div class="report-card"><h4>C454e (Prod. B&N Mes)</h4><p class="data-number"><?php echo $c454e_bn_prod ?? 0; ?> / 950</p></div>
        <div class="report-card"><h4>C454e (Prod. Color Mes)</h4><p class="data-number"><?php echo $c454e_color_prod ?? 0; ?> / 500</p></div>
        <div class="report-card">
            <h4>Reinicio de Producción</h4>
            <p class="data-number"><?php echo (int)date('t') - (int)date('j'); ?> días</p>
            <small>Para el 1ro del próximo mes</small>
        </div>
    </div>
</div>

<div class="report-section">
    <h3>Gestión de Contadores Manuales</h3>
    <div class="card-grid">
        <div class="report-card form-card">
            <h4>Registrar Contador</h4>
            <form id="counter-form" action="/sistemagestion/reports/store_counter" method="POST">
                <select name="maquina" id="maquina-selector" required><option value="Bh-227">Bh-227</option><option value="C454e">C454e</option></select>
                <input type="date" name="fecha_inicio" required title="Fecha de Inicio del Período">
                <input type="date" name="fecha_fin" required title="Fecha de Fin del Período" value="<?php echo date('Y-m-d'); ?>">
                <input type="number" name="contador_bn" placeholder="Contador B&N" required>
                <input type="number" name="contador_color" id="contador-color" placeholder="Contador Color">
                <button type="submit">Registrar</button>
            </form>
        </div>
        <div class="report-card wide-card">
            <h4>Historial de Contadores</h4>
            <form action="/sistemagestion/reports" method="GET" class="inline-form">
                <input type="month" name="counter_month" value="<?php echo htmlspecialchars($_GET['counter_month'] ?? ''); ?>">
                <button type="submit">Filtrar por Mes</button>
            </form>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th><input type="checkbox" id="select-all-counters"></th><th>Máquina</th><th>Período</th><th>B&N</th><th>Color</th></tr></thead>
                    <tbody id="counters-history-body">
                        </tbody>
                </table>
            </div>
            <button id="delete-selected-counters" class="action-btn delete" style="margin-top: 10px;">Eliminar Seleccionados</button>
        </div>
    </div>
</div>

<div class="report-section">
    <h3>Gestión de Proveedor (Gramar)</h3>
    <div class="provider-section">
        <div class="alert"><strong>Próximo Vencimiento:</strong> 1 de <?php echo $nombreProximoMes; ?></div>
        <div class="card-grid">
            <div class="report-card form-card">
                <h4>Registrar Pago a Proveedor</h4>
                <form id="payment-form" action="/sistemagestion/reports/store_payment" method="POST">
                    <input type="date" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required>
                    <input type="text" name="descripcion" placeholder="Descripción (ej: Alquiler Agosto)" required>
                    <input type="number" name="monto" step="0.01" placeholder="Monto" required>
                    <button type="submit">Registrar Pago</button>
                </form>
            </div>
            <div class="report-card wide-card">
                <h4>Historial de Pagos</h4>
                 <form action="/sistemagestion/reports" method="GET" class="inline-form">
                    <input type="month" name="payment_month" value="<?php echo htmlspecialchars($_GET['payment_month'] ?? ''); ?>">
                    <button type="submit">Filtrar por Mes</button>
                </form>
                <div class="table-container">
                    <table class="table">
                        <thead><tr><th><input type="checkbox" id="select-all-payments"></th><th>Fecha</th><th>Descripción</th><th>Monto</th></tr></thead>
                        <tbody id="payments-history-body">
                            </tbody>
                    </table>
                </div>
                <button id="delete-selected-payments" class="action-btn delete" style="margin-top: 10px;">Eliminar Seleccionados</button>
            </div>
        </div>
    </div>
</div>

<style>
    h2 { font-size: 28px; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 30px; }
    .report-section { margin-bottom: 40px; }
    .report-section h3 { font-size: 20px; color: #333; margin-bottom: 20px; }
    .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
    .report-card { background-color: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s; }
    a.report-card:hover { transform: translateY(-5px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .report-card h4 { margin-top: 0; margin-bottom: 10px; font-size: 16px; color: #6c757d; }
    .report-card p.data-number { font-size: 2.2em; font-weight: 600; color: #007bff; margin: 0; }
    .report-card small { color: #6c757d; }
    .filter-form, .form-card form { display: flex; flex-direction: column; gap: 10px; }
    .inline-form { flex-direction: row; align-items: center; justify-content: center; margin-bottom: 15px; flex-wrap: wrap; }
    .filter-form { flex-direction: row; align-items: end; margin-bottom: 20px; background-color: #f8f9fa; padding: 15px; border-radius: 8px; flex-wrap: wrap; }
    .main-filter { justify-content: center; font-size: 1.1em; }
    .main-filter label { font-weight: bold; }
    .status-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    .provider-section .alert { background-color: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeeba; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    .report-card.wide-card { grid-column: 1 / -1; }
    @media (min-width: 992px) { .report-card.wide-card { grid-column: span 2; } }
    .table-container { max-height: 220px; overflow-y: auto; text-align: left; }
    .action-btn.delete { background-color: #dc3545; color:white; border:none; border-radius:5px; padding: 5px 10px; cursor:pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let paymentsData = <?php echo json_encode($providerPayments ?? []); ?>;
    let countersData = <?php echo json_encode($counterHistory ?? []); ?>;

    const paymentsBody = document.getElementById('payments-history-body');
    const paymentForm = document.getElementById('payment-form');
    const countersBody = document.getElementById('counters-history-body');
    const counterForm = document.getElementById('counter-form');

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return new Date(dateString + 'T00:00:00').toLocaleDateString('es-ES', options);
    }

    function renderPaymentsTable(payments) {
        paymentsBody.innerHTML = '';
        if(!payments || payments.length === 0) {
            paymentsBody.innerHTML = '<tr><td colspan="4">No hay pagos para el filtro aplicado.</td></tr>';
            return;
        }
        payments.forEach(p => {
            const row = `<tr>
                <td><input type="checkbox" class="payment-checkbox" value="${p.id}"></td>
                <td>${formatDate(p.fecha_pago)}</td>
                <td>${p.descripcion}</td>
                <td>$${parseFloat(p.monto).toFixed(2)}</td>
            </tr>`;
            paymentsBody.innerHTML += row;
        });
    }

    if (paymentForm) {
        paymentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            await fetch('/sistemagestion/reports/store_provider_payment', { method: 'POST', body: formData });
            
            const newPayment = Object.fromEntries(formData.entries());
            newPayment.id = Date.now();
            paymentsData.unshift(newPayment);
            renderPaymentsTable(paymentsData);
            this.reset();
        });
    }

    document.getElementById('delete-selected-payments')?.addEventListener('click', async () => {
        const selectedIds = Array.from(document.querySelectorAll('.payment-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} pago(s)?`)) {
            await fetch('/sistemagestion/reports/delete_payments', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `ids[]=${selectedIds.join('&ids[]=')}`
            });
            paymentsData = paymentsData.filter(p => !selectedIds.includes(String(p.id)));
            renderPaymentsTable(paymentsData);
        }
    });
    
    document.getElementById('select-all-payments')?.addEventListener('change', e => {
        document.querySelectorAll('.payment-checkbox').forEach(cb => cb.checked = e.target.checked);
    });

    function renderCountersTable(counters) {
        countersBody.innerHTML = '';
        if(!counters || counters.length === 0) {
            countersBody.innerHTML = '<tr><td colspan="5">No hay contadores para el filtro aplicado.</td></tr>';
            return;
        }
        counters.forEach(c => {
            const row = `<tr>
                <td><input type="checkbox" class="counter-checkbox" value="${c.id}"></td>
                <td>${c.maquina_nombre}</td>
                <td>${formatDate(c.fecha_inicio)} - ${formatDate(c.fecha_fin)}</td>
                <td>${c.contador_bn}</td>
                <td>${c.contador_color}</td>
            </tr>`;
            countersBody.innerHTML += row;
        });
    }
    
    if (counterForm) {
        counterForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            await fetch('/sistemagestion/reports/store_counter', { method: 'POST', body: formData });
            
            const newCounter = Object.fromEntries(formData.entries());
            newCounter.id = Date.now();
            newCounter.maquina_nombre = newCounter.maquina;
            countersData.unshift(newCounter);
            renderCountersTable(countersData);
            this.reset();
            document.getElementById('maquina-selector').dispatchEvent(new Event('change'));
        });
    }

     document.getElementById('delete-selected-counters')?.addEventListener('click', async () => {
        const selectedIds = Array.from(document.querySelectorAll('.counter-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length > 0 && confirm(`¿Seguro que quieres eliminar ${selectedIds.length} registro(s)?`)) {
            await fetch('/sistemagestion/reports/delete_counters', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `ids[]=${selectedIds.join('&ids[]=')}`
            });
            countersData = countersData.filter(c => !selectedIds.includes(String(c.id)));
            renderCountersTable(countersData);
        }
    });

    document.getElementById('select-all-counters')?.addEventListener('change', e => {
        document.querySelectorAll('.counter-checkbox').forEach(cb => cb.checked = e.target.checked);
    });

    document.getElementById('maquina-selector').addEventListener('change', function(){
        const colorInput = document.getElementById('contador-color');
        if (this.value === 'Bh-227') {
            colorInput.style.display = 'none';
            colorInput.value = '';
        } else {
            colorInput.style.display = 'block';
        }
    }).dispatchEvent(new Event('change'));

    renderPaymentsTable(paymentsData);
    renderCountersTable(countersData);
});
</script>