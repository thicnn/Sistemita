<h2 class="mb-4">Productos Comprados Juntos Frecuentemente</h2>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Top 50 Pares de Productos Más Comunes</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Producto A</th>
                        <th>Producto B</th>
                        <th class="text-center">Veces Comprados Juntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($frequentlyBoughtTogether)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted p-4">No se encontraron datos suficientes para generar este reporte.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($frequentlyBoughtTogether as $pair): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pair['producto_a']); ?></td>
                                <td><?php echo htmlspecialchars($pair['producto_b']); ?></td>
                                <td class="text-center fw-bold"><?php echo $pair['veces_juntos']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
