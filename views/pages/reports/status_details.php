<h2>Pedidos con Estado: "<?php echo htmlspecialchars(urldecode($status)); ?>"</h2>
<a href="/sistemagestion/reports" class="button">Volver a Reportes</a>
<table class="table">
    <thead><tr><th>ID</th><th>Cliente</th><th>Costo</th><th>Fecha</th><th>Acciones</th></tr></thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?></td>
            <td>$<?php echo number_format($order['costo_total'], 2); ?></td>
            <td><?php echo date('d/m/Y', strtotime($order['fecha_creacion'])); ?></td>
            <td><a href="/sistemagestion/orders/show/<?php echo $order['id']; ?>">Ver Pedido</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>