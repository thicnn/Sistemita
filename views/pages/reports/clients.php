<h2 class="mb-4">Reportes de Clientes</h2>

<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0">Tendencias por Cliente</h5></div>
    <div class="card-body">
        <form action="/sistemagestion/reports/clients" method="GET" class="row g-3 align-items-center">
            <div class="col-md-10">
                <label for="client_id" class="form-label">Seleccione un cliente para ver sus tendencias de pedidos:</label>
                <select id="client_id" name="client_id" class="form-select">
                    <option value="">-- Seleccione un cliente --</option>
                    <?php foreach ($allClients as $client): ?>
                        <option value="<?php echo $client['id']; ?>" <?php echo (isset($selectedClientId) && $selectedClientId == $client['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($client['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="submit" class="btn btn-primary mt-4">Buscar</button>
            </div>
        </form>

        <?php if ($selectedClientId): ?>
        <hr class="my-4">
        <h5 class="mb-3">Productos Más Pedidos</h5>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Cantidad Total Pedida</th>
                        <th class="text-end">Monto Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productTrends)): ?>
                        <tr><td colspan="3" class="text-center text-muted">No se encontraron tendencias de pedidos para este cliente.</td></tr>
                    <?php else: foreach ($productTrends as $trend): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($trend['descripcion']); ?></td>
                            <td class="text-end"><?php echo $trend['total_cantidad']; ?></td>
                            <td class="text-end">$<?php echo number_format($trend['total_subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Clientes que Ordenan Más o Menos</h5></div>
    <div class="card-body">
        <form action="/sistemagestion/reports/clients" method="GET" class="row g-3 align-items-center mb-4">
            <div class="col-md-4">
                <label for="start_date" class="form-label">Fecha de Inicio</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($topClientsFilters['fecha_inicio']); ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date" class="form-label">Fecha de Fin</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($topClientsFilters['fecha_fin']); ?>">
            </div>
            <div class="col-md-3">
                <label for="sort" class="form-label">Ordenar por</label>
                <select id="sort" name="sort" class="form-select">
                    <option value="total_gastado" <?php echo ($topClientsFilters['sort'] === 'total_gastado') ? 'selected' : ''; ?>>Monto Gastado</option>
                    <option value="total_pedidos" <?php echo ($topClientsFilters['sort'] === 'total_pedidos') ? 'selected' : ''; ?>>Cantidad de Pedidos</option>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-end">Total de Pedidos</th>
                        <th class="text-end">Monto Total Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topClients)): ?>
                        <tr><td colspan="3" class="text-center text-muted">No se encontraron clientes para los filtros seleccionados.</td></tr>
                    <?php else: foreach ($topClients as $client): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($client['nombre']); ?></td>
                            <td class="text-end"><?php echo $client['total_pedidos']; ?></td>
                            <td class="text-end">$<?php echo number_format($client['total_gastado'], 2); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
