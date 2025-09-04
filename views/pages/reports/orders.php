<?php
// Helper function to generate sortable table headers
function get_sort_link($title, $column, $filters) {
    $sort = $filters['sort'];
    $dir = $filters['dir'];
    $icon = '';
    if ($sort === $column) {
        $icon = ($dir === 'ASC') ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>';
        $dir = ($dir === 'ASC') ? 'DESC' : 'ASC';
    } else {
        $dir = 'ASC';
    }
    $queryParams = http_build_query(array_merge($filters, ['sort' => $column, 'dir' => $dir]));
    return "<a href=\"/sistemagestion/reports/orders?$queryParams\">$title $icon</a>";
}
?>

<h2 class="mb-4">Detalle de Pedidos</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/orders" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar por Cliente o ID</label>
                <input type="text" id="search" name="search" class="form-control" value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="col-md-2">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="Pendiente" <?php echo ($filters['estado'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo ($filters['estado'] === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Listo para Retirar" <?php echo ($filters['estado'] === 'Listo para Retirar') ? 'selected' : ''; ?>>Listo para Retirar</option>
                    <option value="Entregado" <?php echo ($filters['estado'] === 'Entregado') ? 'selected' : ''; ?>>Entregado</option>
                    <option value="Cancelado" <?php echo ($filters['estado'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?php echo htmlspecialchars($filters['fecha_inicio']); ?>">
            </div>
            <div class="col-md-3">
                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?php echo htmlspecialchars($filters['fecha_fin']); ?>">
            </div>
            <div class="col-12 text-end">
                <a href="/sistemagestion/reports/orders" class="btn btn-secondary">Limpiar</a>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><?php echo get_sort_link('ID', 'id', $filters); ?></th>
                        <th><?php echo get_sort_link('Cliente', 'nombre_cliente', $filters); ?></th>
                        <th><?php echo get_sort_link('Estado', 'estado', $filters); ?></th>
                        <th class="text-end"><?php echo get_sort_link('Total', 'costo_total', $filters); ?></th>
                        <th><?php echo get_sort_link('Fecha', 'fecha_creacion', $filters); ?></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">No se encontraron pedidos para los filtros seleccionados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['nombre_cliente'] ?? 'Cliente no encontrado'); ?></td>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($order['estado']); ?></span></td>
                                <td class="text-end">$<?php echo number_format($order['costo_total'], 2); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['fecha_creacion'])); ?></td>
                                <td><a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>" class="btn btn-sm btn-info">Ver</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="/sistemagestion/reports/orders?<?php echo http_build_query(array_merge($filters, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
