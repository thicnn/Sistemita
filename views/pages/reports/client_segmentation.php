<h2 class="mb-4">Segmentación de Clientes (RFM)</h2>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Análisis de Clientes por Recencia, Frecuencia y Valor Monetario</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th class="text-center">Última Compra (Días)</th>
                        <th class="text-center">Frecuencia (Pedidos)</th>
                        <th class="text-end">Valor Monetario</th>
                        <th class="text-center">Segmento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">No hay datos suficientes para generar este reporte.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($client['nombre']); ?></td>
                                <td class="text-center"><?php echo $client['recencia']; ?></td>
                                <td class="text-center"><?php echo $client['frecuencia']; ?></td>
                                <td class="text-end">$<?php echo number_format($client['monetario'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        <?php echo htmlspecialchars($client['segmento']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
