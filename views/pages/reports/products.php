<h2 class="mb-4">Reportes de Productos</h2>

<div class="card shadow-sm mb-4">
    <div class="card-header"><h5 class="mb-0">Buscar Pedidos por Producto</h5></div>
    <div class="card-body">
        <form action="/sistemagestion/reports/products" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($tipos as $tipo): ?>
                        <option value="<?= $tipo['tipo']; ?>" <?= (isset($productFilters['tipo']) && $productFilters['tipo'] == $tipo['tipo']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($tipo['tipo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="maquina_id" class="form-label">Máquina</label>
                <select id="maquina_id" name="maquina_id" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($maquinas as $maquina): ?>
                        <option value="<?= $maquina['maquina_id']; ?>" <?= (isset($productFilters['maquina_id']) && $productFilters['maquina_id'] == $maquina['maquina_id']) ? 'selected' : ''; ?>>
                            <?= ($maquina['maquina_id'] == 1) ? 'Bh-227' : 'C454e'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas</option>
                     <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['categoria']; ?>" <?= (isset($productFilters['categoria']) && $productFilters['categoria'] == $categoria['categoria']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($categoria['categoria']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Descripción</label>
                <input type="text" id="search" name="search" class="form-control" value="<?= htmlspecialchars($productFilters['search']); ?>">
            </div>
            <div class="col-12 text-end">
                <a href="/sistemagestion/reports/products" class="btn btn-secondary">Limpiar</a>
                <button type="submit" class="btn btn-primary">Buscar Productos</button>
            </div>
        </form>

        <hr class="my-4">
        <form action="/sistemagestion/reports/products" method="GET">
            <!-- Hidden fields to preserve filters -->
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($productFilters['tipo']); ?>">
            <input type="hidden" name="maquina_id" value="<?= htmlspecialchars($productFilters['maquina_id']); ?>">
            <input type="hidden" name="categoria" value="<?= htmlspecialchars($productFilters['categoria']); ?>">
            <input type="hidden" name="search" value="<?= htmlspecialchars($productFilters['search']); ?>">

            <div class="row g-3 align-items-center">
                <div class="col-md-10">
                    <label for="product_id" class="form-label">Seleccione un producto de la lista para ver sus pedidos:</label>
                    <select id="product_id" name="product_id" class="form-select">
                        <option value="">-- Productos Filtrados --</option>
                        <?php foreach ($allProducts as $product): ?>
                            <option value="<?= $product['id']; ?>" <?= (isset($selectedProductId) && $selectedProductId == $product['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($product['descripcion']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="submit" class="btn btn-info mt-4">Ver Pedidos</button>
                </div>
            </div>
        </form>

        <?php if ($selectedProductId): ?>
        <hr class="my-4">
        <h5 class="mb-3">Resultados de la Búsqueda</h5>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ordersByProduct)): ?>
                        <tr><td colspan="6" class="text-center text-muted">No se encontraron pedidos para este producto.</td></tr>
                    <?php else: foreach ($ordersByProduct as $order): ?>
                        <tr>
                            <td><a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>">#<?php echo $order['id']; ?></a></td>
                            <td><?php echo htmlspecialchars($order['nombre_cliente']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($order['estado']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($order['fecha_creacion'])); ?></td>
                            <td class="text-end"><?php echo $order['cantidad']; ?></td>
                            <td class="text-end">$<?php echo number_format($order['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Top 25 Productos Más Vendidos</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Unidades Vendidas</th>
                        <th class="text-end">Ingresos Generados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topSellingProducts)): ?>
                        <tr><td colspan="3" class="text-center text-muted">No hay datos de productos vendidos.</td></tr>
                    <?php else: foreach ($topSellingProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['descripcion']); ?></td>
                            <td class="text-end"><?php echo $product['unidades_vendidas']; ?></td>
                            <td class="text-end">$<?php echo number_format($product['ingresos_generados'], 2); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($topSellingTotalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $topSellingTotalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $topSellingPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="/sistemagestion/reports/products?top_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><h5 class="mb-0">Top 25 Productos Menos Vendidos</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Unidades Vendidas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leastSellingProducts)): ?>
                        <tr><td colspan="2" class="text-center text-muted">No hay datos de productos.</td></tr>
                    <?php else: foreach ($leastSellingProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['descripcion']); ?></td>
                            <td class="text-end"><?php echo $product['unidades_vendidas']; ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($leastSellingTotalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $leastSellingTotalPages; $i++): ?>
                <li class="page-item <?php echo ($i == $leastSellingPage) ? 'active' : ''; ?>">
                    <a class="page-link" href="/sistemagestion/reports/products?least_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
