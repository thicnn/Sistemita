<?php
// Función para generar badges de estado con colores sutiles
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

// Función para generar los enlaces de ordenamiento en la cabecera de la tabla
function sortableLink($column, $text, $currentSort, $currentDir) {
    $dir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
    $arrow = $currentSort === $column ? ($currentDir === 'asc' ? 'bi-sort-up' : 'bi-sort-down') : 'bi-filter';
    // Mantenemos los filtros actuales al ordenar
    $queryParams = http_build_query(array_merge($_GET, ['sort' => $column, 'dir' => $dir]));
    return "<a href='?$queryParams' class='text-decoration-none text-dark'>$text <i class='bi $arrow'></i></a>";
}

$sort = $_GET['sort'] ?? 'fecha_creacion';
$dir = $_GET['dir'] ?? 'desc';
$estados = ["Solicitud", "Cotización", "Confirmado", "En Curso", "Listo para Retirar", "Entregado", "Cancelado"];
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h2 class="mb-0">Gestión de Pedidos</h2>
    <a href="/sistemagestion/orders/create" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-2"></i>Crear Nuevo Pedido
    </a>
</div>

<div class="card shadow-sm mb-4 animated-card">
    <div class="card-header bg-light">
        <a class="text-decoration-none text-dark d-flex justify-content-between" data-bs-toggle="collapse" href="#filtersCollapse" role="button" aria-expanded="false" aria-controls="filtersCollapse">
            <strong><i class="bi bi-funnel-fill me-2"></i>Filtros de Búsqueda</strong>
            <i class="bi bi-chevron-down"></i>
        </a>
    </div>
    <div class="collapse" id="filtersCollapse">
        <div class="card-body">
            <form action="/sistemagestion/orders" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar por Cliente o ID</label>
                        <input type="text" id="search" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado" class="form-select">
                            <option value="">Todos</option>
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?php echo $estado; ?>" <?php echo ($_GET['estado'] ?? '') == $estado ? 'selected' : ''; ?>>
                                    <?php echo $estado; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label">Desde</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($_GET['fecha_inicio'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label">Hasta</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($_GET['fecha_fin'] ?? ''); ?>">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="/sistemagestion/orders" class="btn btn-outline-secondary">Limpiar Filtros</a>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm animated-card" style="animation-delay: 0.1s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th><?php echo sortableLink('id', 'ID', $sort, $dir); ?></th>
                        <th><?php echo sortableLink('nombre_cliente', 'Cliente', $sort, $dir); ?></th>
                        <th class="text-center"><?php echo sortableLink('estado', 'Estado', $sort, $dir); ?></th>
                        <th class="text-end"><?php echo sortableLink('costo_total', 'Total', $sort, $dir); ?></th>
                        <th><?php echo sortableLink('fecha_creacion', 'Fecha', $sort, $dir); ?></th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center p-4 text-muted">No se encontraron pedidos con los filtros aplicados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['nombre_cliente'] ?? 'Sin cliente'); ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?php echo getStatusBadgeClass($order['estado']); ?>">
                                        <?php echo htmlspecialchars($order['estado']); ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold">$<?php echo number_format($order['costo_total'], 2); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['fecha_creacion'])); ?></td>
                                <td class="text-center">
                                    <a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalles">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>