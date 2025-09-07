<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 0; }
        .details { margin-bottom: 20px; }
        .details table { width: 100%; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; }
        .items-table th { background-color: #f2f2f2; text-align: left; }
        .totals { float: right; width: 40%; }
        .totals table { width: 100%; }
        .totals th, .totals td { padding: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Eneieme LTDA "Clave 3 - Impresiones"</h1>
            <p>Recibo</p>
        </div>
        <div class="details">
            <table>
                <tr>
                    <td><strong>Pedido #:</strong> <?php echo $order['id']; ?></td>
                    <td><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></td>
                </tr>
                <tr>
                    <td><strong>Cliente:</strong> <?php echo htmlspecialchars($order['nombre_cliente'] ?? 'N/A'); ?></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th style="text-align:right;">Cantidad</th>
                    <th style="text-align:right;">Precio Unit.</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                        <td style="text-align:right;"><?php echo $item['cantidad']; ?></td>
                        <td style="text-align:right;">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                        <td style="text-align:right;">$<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <table>
                <tr>
                    <th style="text-align:right;">Costo Total:</th>
                    <td style="text-align:right;">$<?php echo number_format($order['costo_total'], 2); ?></td>
                </tr>
                <?php
                $totalPagado = array_sum(array_column($order['pagos'], 'monto'));
                $saldoPendiente = $order['costo_total'] - $totalPagado;
                ?>
                <tr>
                    <th style="text-align:right;">Total Pagado:</th>
                    <td style="text-align:right;">$<?php echo number_format($totalPagado, 2); ?></td>
                </tr>
                <tr>
                    <th style="text-align:right;">Saldo Pendiente:</th>
                    <td style="text-align:right;">$<?php echo number_format($saldoPendiente, 2); ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
