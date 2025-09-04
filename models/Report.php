<?php
class Report
{
    private $connection;

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    public function countOrdersByStatus($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT estado, COUNT(id) as total FROM pedidos WHERE fecha_creacion BETWEEN ? AND ? GROUP BY estado";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // --- NUEVO MÉTODO ---
    /**
     * Obtiene un resumen de ventas y pedidos de los últimos 6 meses para un gráfico.
     */
    public function getSalesOverTime()
    {
        $query = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    SUM(costo_total) as total_ventas,
                    COUNT(id) as total_pedidos
                  FROM pedidos
                  WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND estado = 'Entregado'
                  GROUP BY mes
                  ORDER BY mes ASC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    // --- NUEVO MÉTODO ---
    /**
     * Obtiene los productos más vendidos en un período de tiempo.
     */
    public function getTopSellingProducts($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT 
                    prod.descripcion,
                    SUM(i.cantidad) as unidades_vendidas,
                    SUM(i.subtotal) as ingresos_generados
                  FROM items_pedido i
                  JOIN pedidos p ON i.pedido_id = p.id
                  JOIN productos prod ON i.producto_id = prod.id
                  WHERE p.estado = 'Entregado' AND p.fecha_creacion BETWEEN ? AND ?
                  GROUP BY prod.id, prod.descripcion
                  ORDER BY ingresos_generados DESC
                  LIMIT 10";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getProfitOverTime()
    {
        $query = "SELECT
                    DATE_FORMAT(p.fecha_creacion, '%Y-%m') as month,
                    SUM(i.subtotal) as revenue,
                    SUM(
                        CASE
                            WHEN prod.tipo = 'Servicio' THEN 0
                            WHEN prod.maquina_id = 1 THEN -- BH-227
                                i.cantidad * (0.83 + CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END)
                            WHEN prod.maquina_id = 2 THEN -- C454e
                                i.cantidad * (
                                    (CASE WHEN prod.descripcion LIKE '%Color%' THEN 10.0 ELSE 2.3 END) +
                                    (CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END)
                                )
                            ELSE 0
                        END
                    ) as cost
                FROM pedidos p
                JOIN items_pedido i ON p.id = i.pedido_id
                JOIN productos prod ON i.producto_id = prod.id
                WHERE p.estado = 'Entregado' AND p.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY month
                ORDER BY month ASC";

        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getProductionCountForPeriod($maquina_id, $tipo, $categoria, $inicio, $fin)
    {
        $fin_completa = $fin . ' 23:59:59';
        $query = "SELECT SUM(i.cantidad) as total FROM items_pedido i JOIN pedidos p ON i.pedido_id = p.id JOIN productos pr ON i.descripcion = pr.descripcion WHERE pr.maquina_id = ? AND i.tipo = ? AND i.categoria = ? AND p.estado IN ('Listo para Retirar', 'Entregado') AND p.fecha_creacion BETWEEN ? AND ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("issss", $maquina_id, $tipo, $categoria, $inicio, $fin_completa);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getCounterHistory($filters = [])
    {
        $query = "SELECT * FROM impresora_contadores";
        if (!empty($filters['month'])) {
            $query .= " WHERE DATE_FORMAT(fecha_fin, '%Y-%m') = ?";
        }
        $query .= " ORDER BY fecha_fin DESC";
        $stmt = $this->connection->prepare($query);
        if (!empty($filters['month'])) {
            $stmt->bind_param("s", $filters['month']);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveCounter($maquina, $fecha_inicio, $fecha_fin, $bn, $color, $notas)
    {
        $query = "INSERT INTO impresora_contadores (maquina_nombre, fecha_inicio, fecha_fin, contador_bn, contador_color, notas) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $color = empty($color) ? 0 : $color;
        $stmt->bind_param("sssiis", $maquina, $fecha_inicio, $fecha_fin, $bn, $color, $notas);
        return $stmt->execute();
    }

    public function getProviderPayments($filters = [])
    {
        $query = "SELECT * FROM proveedor_pagos";
        $where = [];
        $params = [];
        $types = '';
        if (!empty($filters['month'])) {
            $where[] = "DATE_FORMAT(fecha_pago, '%Y-%m') = ?";
            $params[] = $filters['month'];
            $types .= 's';
        }
        if (!empty($filters['amount']) && is_numeric($filters['amount'])) {
            $where[] = "monto >= ?";
            $params[] = $filters['amount'];
            $types .= 'd';
        }
        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        $query .= " ORDER BY fecha_pago DESC";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveProviderPayment($fecha, $descripcion, $monto)
    {
        $query = "INSERT INTO proveedor_pagos (fecha_pago, descripcion, monto) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssd", $fecha, $descripcion, $monto);
        return $stmt->execute();
    }

    public function deleteProviderPayment($id)
    {
        $stmt = $this->connection->prepare("DELETE FROM proveedor_pagos WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteAllCounters()
    {
        $this->connection->query("TRUNCATE TABLE `impresora_contadores`");
        return true;
    }

    public function deleteAllProviderPayments()
    {
        $this->connection->query("TRUNCATE TABLE `proveedor_pagos`");
        return true;
    }

    public function deleteCounters($ids)
    {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->connection->prepare("DELETE FROM impresora_contadores WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        return $stmt->execute();
    }

    public function deleteProviderPayments($ids)
    {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->connection->prepare("DELETE FROM proveedor_pagos WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        return $stmt->execute();
    }

    public function getServicesReport($fechaInicio, $fechaFin)
    {
        $fin_completa = $fechaFin . ' 23:59:59';
        $query = "SELECT 
                    i.descripcion, 
                    SUM(i.cantidad) as total_cantidad, 
                    COUNT(DISTINCT i.pedido_id) as total_pedidos
                  FROM items_pedido i
                  JOIN pedidos p ON i.pedido_id = p.id
                  WHERE p.estado IN ('Entregado', 'Listo para Retirar') AND p.es_interno = 0 AND p.fecha_creacion BETWEEN ? AND ?
                  GROUP BY i.descripcion
                  ORDER BY total_cantidad DESC";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fin_completa);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getLosses($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT SUM(costo_total) as total_losses FROM pedidos_errores WHERE fecha_creacion BETWEEN ? AND ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_losses'] ?? 0;
    }

    public function getProfitByDateRange($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT
                    i.cantidad,
                    i.subtotal,
                    prod.descripcion,
                    prod.maquina_id,
                    prod.tipo
                  FROM items_pedido i
                  JOIN pedidos p ON i.pedido_id = p.id
                  JOIN productos prod ON i.producto_id = prod.id
                  WHERE p.estado = 'Entregado' AND p.fecha_creacion BETWEEN ? AND ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $total_revenue = 0;
        $total_cost = 0;

        foreach ($items as $item) {
            $total_revenue += (float)$item['subtotal'];
            $costo_item = 0;

            if ($item['tipo'] !== 'Servicio') {
                $cantidad = (int)$item['cantidad'];
                $costo_unitario = 0;
                $costo_papel = (stripos($item['descripcion'], 'A4') !== false) ? 0.35 : 7.0;
                $costo_impresion = 0;

                if ($item['maquina_id'] == 1) { // BH-227 (B&W only)
                    $costo_impresion = 0.83;
                } elseif ($item['maquina_id'] == 2) { // C454e
                    if (stripos($item['descripcion'], 'Color') !== false) {
                        $costo_impresion = 10.0;
                    } else {
                        $costo_impresion = 2.3;
                    }
                }
                $costo_unitario = $costo_impresion + $costo_papel;
                $costo_item = $costo_unitario * $cantidad;
            }
            $total_cost += $costo_item;
        }

        return [
            'total_profit' => $total_revenue - $total_cost,
            'total_revenue' => $total_revenue,
            'total_cost' => $total_cost,
        ];
    }

    public function getPrinterCounters($month)
    {
        $query = "SELECT
                    i.cantidad,
                    prod.descripcion,
                    prod.maquina_id
                  FROM items_pedido i
                  JOIN pedidos p ON i.pedido_id = p.id
                  JOIN productos prod ON i.producto_id = prod.id
                  WHERE p.estado IN ('Listo para Retirar', 'Entregado')
                  AND DATE_FORMAT(p.fecha_creacion, '%Y-%m') = ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $month);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $counters = [
            'bh227_bw' => 0,
            'c454e_bw' => 0,
            'c454e_color' => 0,
        ];

        foreach($items as $item) {
            $cantidad = (int)$item['cantidad'];
            if ($item['maquina_id'] == 1) { // BH-227
                $counters['bh227_bw'] += $cantidad;
            } elseif ($item['maquina_id'] == 2) { // C454e
                if (stripos($item['descripcion'], 'Color') !== false) {
                    $counters['c454e_color'] += $cantidad;
                } else {
                    $counters['c454e_bw'] += $cantidad;
                }
            }
        }
        return $counters;
    }

    private function addBusinessDays($startDate, $days) {
        $currentDate = new DateTime($startDate);
        // Start counting from the next day
        $currentDate->modify('+1 day');

        while ($days > 0) {
            $dayOfWeek = $currentDate->format('w');
            if ($dayOfWeek != 0 && $dayOfWeek != 6) { // 0=Sunday, 6=Saturday
                $days--;
            }
            if ($days > 0) {
                $currentDate->modify('+1 day');
            }
        }
        return $currentDate;
    }

    public function getSalesDistribution($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT monto, metodo_pago, fecha_pago FROM pagos WHERE fecha_pago BETWEEN ? AND ?";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $distribution = ['efectivo' => 0, 'debitado' => 0, 'pendiente' => 0];
        $now = new DateTime();

        foreach ($payments as $payment) {
            $monto = (float)$payment['monto'];

            if ($payment['metodo_pago'] === 'Efectivo') {
                $distribution['efectivo'] += $monto;
            } else {
                $creditDate = null;
                $paymentDate = new DateTime($payment['fecha_pago']);

                if ($payment['metodo_pago'] === 'Débito') {
                    $creditDate = $this->addBusinessDays($payment['fecha_pago'], 1);
                } elseif ($payment['metodo_pago'] === 'Crédito') {
                    $creditDate = $this->addBusinessDays($payment['fecha_pago'], 2);
                }

                if ($creditDate) {
                    $creditDate->setTime(15, 0, 0);
                    if ($now >= $creditDate) {
                        $distribution['debitado'] += $monto;
                    } else {
                        $distribution['pendiente'] += $monto;
                    }
                }
            }
        }
        return $distribution;
    }
}
