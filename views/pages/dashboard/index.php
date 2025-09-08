<?php
// Función auxiliar para los badges
function getStatusBadgeClass($status)
{
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

<div class="row g-4" id="dashboard-widgets-container">
    <div class="col-lg-8 widget-column" data-widget-id="main-column">
        <div class="drag-handle text-muted text-center mb-2"><i class="bi bi-grip-horizontal"></i></div>
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

        <!-- Alertas de Stock Bajo -->
        <?php if (isset($lowStockMaterials) && !empty($lowStockMaterials) && $_SESSION['user_role'] === 'administrador') : ?>
            <div class="card border-warning shadow-sm mt-4 animated-card" style="animation-delay: 0.15s;">
                <div class="card-header bg-warning-subtle text-warning-emphasis d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 h6">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Alertas de Stock de Materiales
                    </h5>
                    <span class="badge bg-warning text-dark"><?php echo count($lowStockMaterials); ?></span>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($lowStockMaterials as $material) : ?>
                        <a href="/sistemagestion/admin/materials/edit/<?php echo $material['id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($material['nombre']); ?></strong>
                                <small class="d-block text-danger fw-bold">
                                    Quedan <?php echo number_format($material['stock_actual'], 2); ?> / Mínimo: <?php echo number_format($material['stock_minimo'], 2); ?> <?php echo htmlspecialchars($material['unidad_medida']); ?>
                                </small>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-muted"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

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
            <div class="card-header border-0">
                <h5 class="mb-0">Actividad Reciente</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (empty($activityFeed)): ?>
                        <li class="list-group-item text-muted">No hay actividad reciente.</li>
                    <?php else: ?>
                        <?php foreach($activityFeed as $activity): ?>
                            <li class="list-group-item d-flex align-items-center">
                                <?php
                                    $icon = '';
                                    $text = '';
                                    $link = '#';
                                    switch ($activity['type']) {
                                        case 'pedido':
                                            $icon = 'bi-receipt text-primary';
                                            $text = "Nuevo pedido <strong>#" . $activity['data']['id'] . "</strong> de " . htmlspecialchars($activity['data']['nombre_cliente'] ?? 'Consumidor Final');
                                            $link = "/sistemagestion/orders/show/" . $activity['data']['id'];
                                            break;
                                        case 'cliente':
                                            $icon = 'bi-person-plus-fill text-success';
                                            $text = "Nuevo cliente registrado: <strong>" . htmlspecialchars($activity['data']['nombre']) . "</strong>";
                                            $link = "/sistemagestion/clients/show/" . $activity['data']['id'];
                                            break;
                                        case 'pago':
                                            $icon = 'bi-cash-coin text-info';
                                            $text = "Pago de <strong>$" . number_format($activity['data']['monto'], 2) . "</strong> recibido para el pedido #" . $activity['data']['pedido_id'];
                                            $link = "/sistemagestion/orders/show/" . $activity['data']['pedido_id'];
                                            break;
                                    }
                                ?>
                                <i class="bi <?php echo $icon; ?> fs-4 me-3"></i>
                                <div class="flex-grow-1">
                                    <a href="<?php echo $link; ?>" class="text-decoration-none text-body"><?php echo $text; ?></a>
                                    <small class="d-block text-muted"><?php echo (new DateTime($activity['date']))->format('d M Y, H:i'); ?></small>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4 widget-column" data-widget-id="sidebar-column">
        <div class="drag-handle text-muted text-center mb-2"><i class="bi bi-grip-horizontal"></i></div>
        <div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.4s;">
            <div class="card-header border-0">
                <h5 class="mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="/sistemagestion/orders/create" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Nuevo Pedido</a>
                <a href="/sistemagestion/clients/create" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-2"></i>Nuevo Cliente</a>
            </div>
        </div>

        <!-- Metas Mensuales -->
        <?php if (isset($goalsData) && ($_SESSION['user_role'] === 'administrador') && ($goalsData['sales']['goal'] > 0 || $goalsData['orders']['goal'] > 0)) : ?>
        <div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.45s;">
            <div class="card-header border-0">
                <h5 class="mb-0">Progreso Mensual</h5>
            </div>
            <div class="card-body">
                <?php if ($goalsData['sales']['goal'] > 0) : ?>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Meta de Ventas</span>
                            <span class="fw-bold">$<?php echo number_format($goalsData['sales']['current'], 2); ?> / $<?php echo number_format($goalsData['sales']['goal'], 2); ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo min(100, $goalsData['sales']['percent']); ?>%;" aria-valuenow="<?php echo $goalsData['sales']['percent']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($goalsData['sales']['percent']); ?>%</div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($goalsData['orders']['goal'] > 0) : ?>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <span>Meta de Pedidos</span>
                            <span class="fw-bold"><?php echo $goalsData['orders']['current']; ?> / <?php echo $goalsData['orders']['goal']; ?></span>
                        </div>
                        <div class="progress mt-1" style="height: 20px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min(100, $goalsData['orders']['percent']); ?>%;" aria-valuenow="<?php echo $goalsData['orders']['percent']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($goalsData['orders']['percent']); ?>%</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4 animated-card" style="animation-delay: 0.5s;">
            <div class="card-header border-0">
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
            <div class="card-header border-0">
                <h5 class="mb-0">Top 5 Clientes (3 Meses)</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($topClients)): ?>
                    <li class="list-group-item">No hay datos suficientes.</li>
                <?php else: ?>
                    <?php foreach ($topClients as $client): ?>
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
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-card {
        opacity: 0;
        animation: slideInUp 0.6s ease-out forwards;
    }

    /* Tarjetas de sugerencias */
    .suggestion-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid transparent;
        /* Borde transparente por defecto */
        border-radius: 0.5rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease-in-out;
        height: 100%;
    }

    .suggestion-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--bs-box-shadow-sm);
        border-color: var(--bs-primary);
    }

    .suggestion-card strong {
        display: block;
        margin-bottom: 0.25rem;
    }

    .suggestion-card p {
        font-size: 0.9em;
        color: var(--bs-secondary-color);
        margin: 0;
    }

    .fw-medium {
        font-weight: 500 !important;
    }
    .drag-handle {
        cursor: move;
        padding: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reloj
        const timeElement = document.getElementById('time');
        const dateElement = document.getElementById('date');

        function updateClock() {
            const now = new Date();
            timeElement.textContent = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
            dateElement.textContent = now.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }

        updateClock();
        setInterval(updateClock, 1000);

        // Lógica de widgets arrastrables
        const container = document.getElementById('dashboard-widgets-container');
        if (container) {
            const sortable = new Sortable(container, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function() {
                    const order = Array.from(container.children).map(item => item.dataset.widgetId);
                    localStorage.setItem('dashboardWidgetOrder', JSON.stringify(order));
                }
            });

            // Cargar el orden guardado
            const savedOrder = localStorage.getItem('dashboardWidgetOrder');
            if (savedOrder) {
                const order = JSON.parse(savedOrder);
                order.forEach(widgetId => {
                    const widget = container.querySelector(`[data-widget-id="${widgetId}"]`);
                    if (widget) {
                        container.appendChild(widget);
                    }
                });
            }
        }
    });
</script>