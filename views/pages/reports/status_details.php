<?php
// Helper function to generate sortable table headers
function get_sort_link_status($title, $column, $filters, $status) {
    $sort = $filters['sort'];
    $dir = $filters['dir'];
    $icon = '';
    if ($sort === $column) {
        $icon = ($dir === 'ASC') ? '<i class="bi bi-sort-up"></i>' : '<i class="bi bi-sort-down"></i>';
        $dir = ($dir === 'ASC') ? 'DESC' : 'ASC';
    } else {
        $dir = 'ASC';
    }
    unset($filters['estado']);
    $queryParams = http_build_query(array_merge($filters, ['sort' => $column, 'dir' => $dir]));
    return "<a href=\"/sistemagestion/reports/status/" . urlencode($status) . "?$queryParams\">$title $icon</a>";
}
?>

<h2 class="mb-4">Pedidos con Estado: "<?= htmlspecialchars(urldecode($status)); ?>"</h2>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="/sistemagestion/reports/status/<?= urlencode($status); ?>" method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Buscar por Cliente o ID</label>
                <input type="text" id="search" name="search" class="form-control" value="<?= htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="col-md-4">
                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($filters['fecha_inicio']); ?>">
            </div>
            <div class="col-md-4">
                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($filters['fecha_fin']); ?>">
            </div>
            <div class="col-12 text-end">
                <a href="/sistemagestion/reports/status/<?= urlencode($status); ?>" class="btn btn-secondary">Limpiar</a>
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
                        <th><?= get_sort_link_status('ID', 'id', $filters, $status); ?></th>
                        <th><?= get_sort_link_status('Cliente', 'nombre_cliente', $filters, $status); ?></th>
                        <th class="text-end"><?= get_sort_link_status('Total', 'costo_total', $filters, $status); ?></th>
                        <th><?= get_sort_link_status('Fecha', 'fecha_creacion', $filters, $status); ?></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No se encontraron pedidos para los filtros seleccionados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id']; ?></td>
                                <td><?= htmlspecialchars($order['nombre_cliente']); ?></td>
                                <td class="text-end">$<?= number_format($order['costo_total'], 2); ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($order['fecha_creacion'])); ?></td>
                                <td><a href="/sistemagestion/orders/show/<?= $order['id']; ?>" class="btn btn-sm btn-info">Ver</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++):
                    unset($filters['estado']);
                    $queryParams = http_build_query(array_merge($filters, ['page' => $i]));
                ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="/sistemagestion/reports/status/<?= urlencode($status); ?>?<?= $queryParams; ?>"><?= $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>