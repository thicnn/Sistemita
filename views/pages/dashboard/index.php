<?php
// Función auxiliar para los badges
function getStatusBadgeClass($status) {
    $map = [
        'Entregado' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        'Listo para Retirar' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'En Curso' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'Confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'Cancelado' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        'Solicitud' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        'Cotización' => 'bg-light text-dark border'
    ];
    return $map[$status] ?? 'bg-dark';
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="mb-1">¡Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h2>
        <p class="text-muted mb-0">Este es el resumen de tu actividad para hoy.</p>
    </div>
    <div class="text-end">
        <div id="date" class="text-muted"></div>
        <div id="time" class="fs-4 fw-light"></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-4">
            <div class="col-md-6 animated-card">
                <div class="card h-100 shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted mb-1">Pedidos de Hoy</div>
                            <div class="fs-1 fw-bold text-primary"><?php echo $dashboardStats['todays_orders']; ?></div>
                        </div>
                        <i class="bi bi-journal-text fs-1 text-primary-subtle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animated-card" style="animation-delay: 0.1s;">
                <div class="card h-100 shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted mb-1">Ventas del Día</div>
                            <div class="fs-1 fw-bold text-success">$<?php echo number_format($dashboardStats['todays_sales'], 2); ?></div>
                        </div>
                        <i class="bi bi-cash-coin fs-1 text-success-subtle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-2 animated-card" style="animation-delay: 0.2s;">
            <h4 class="mb-3">Sugerencias del Día</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="/sistemagestion/admin/products" class="suggestion-card">
                        <i class="bi bi-pencil-square fs-3 text-primary"></i>
                        <div>
                            <strong>Administrar Catálogo</strong>
                            <p>Revisa tus productos y actualiza precios.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="/sistemagestion/reports" class="suggestion-card">
                        <i class="bi bi-graph-up fs-3 text-success"></i>
                        <div>
                            <strong>Revisar Contadores</strong>
                            <p>Lleva el control de stock y producción.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4 animated-card" style="animation-delay: 0.3s;">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Últimos Pedidos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td class="text-center p-3 text-muted">No hay pedidos recientes.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td class="ps-3"><strong>#<?php echo $order['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?></td>
                                        <td><span class="badge rounded-pill <?php echo getStatusBadgeClass($order['estado']); ?>"><?php echo $order['estado']; ?></span></td>
                                        <td class="text-end fw-medium">$<?php echo number_format($order['costo_total'], 2); ?></td>
                                        <td class="text-end pe-3"><a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.4s;">
             <div class="card-header bg-white border-0">
                <h5 class="mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="/sistemagestion/orders/create" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Nuevo Pedido</a>
                <a href="/sistemagestion/clients/create" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.5s;">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Cola de Producción</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-around text-center">
                    <div>
                        <div class="text-muted">En Curso</div>
                        <div class="fs-1 fw-bold text-primary"><?php echo count($pedidosPorEstado['En Curso'] ?? []); ?></div>
                    </div>
                    <div class="border-start"></div>
                    <div>
                        <div class="text-muted">Para Retirar</div>
                        <div class="fs-1 fw-bold text-info"><?php echo count($pedidosPorEstado['Listo para Retirar'] ?? []); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm animated-card" style="animation-delay: 0.6s;">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Top 5 Clientes (3 Meses)</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($topClients)): ?>
                    <li class="list-group-item">No hay datos suficientes.</li>
                <?php else: ?>
                    <?php foreach($topClients as $client): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($client['nombre']); ?>
                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                <?php echo $client['total_pedidos']; ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Animación de despliegue */
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animated-card {
        opacity: 0;
        animation: slideInUp 0.6s ease-out forwards;
    }
    
    /* Tarjetas de sugerencias */
    .suggestion-card {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem; background-color: #f8f9fa;
        border: 1px solid #e9ecef; border-radius: 0.5rem;
        text-decoration: none; color: inherit;
        transition: all 0.2s ease-in-out; height: 100%;
    }
    .suggestion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1);
        border-color: var(--bs-primary);
    }
    .suggestion-card strong { display: block; margin-bottom: 0.25rem; }
    .suggestion-card p { font-size: 0.9em; color: var(--bs-secondary-color); margin: 0; }
    .card-header.bg-white { background-color: #fff !important; }
    .fw-medium { font-weight: 500 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reloj
    const timeElement = document.getElementById('time');
    const dateElement = document.getElementById('date');
    
    function updateClock() {
        const now = new Date();
        timeElement.textContent = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        dateElement.textContent = now.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
    }

    updateClock();
    setInterval(updateClock, 1000);
});
</script>