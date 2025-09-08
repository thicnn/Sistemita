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

    public function getMonthlySales() {
        $query = "SELECT SUM(costo_total) as total
                  FROM pedidos
                  WHERE estado = 'Entregado' AND MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();
        return (float)($row['total'] ?? 0);
    }

    public function getMonthlyOrders() {
        $query = "SELECT COUNT(id) as total
                  FROM pedidos
                  WHERE estado != 'Cancelado' AND MONTH(fecha_creacion) = MONTH(CURDATE()) AND YEAR(fecha_creacion) = YEAR(CURDATE())";
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    public function findRecentImportantPayments($limit, $min_amount = 500) {
        $query = "SELECT p.monto, p.fecha_pago, c.nombre as nombre_cliente, p.pedido_id
                  FROM pagos p
                  JOIN pedidos ped ON p.pedido_id = ped.id
                  LEFT JOIN clientes c ON ped.cliente_id = c.id
                  WHERE p.monto >= ?
                  ORDER BY p.fecha_pago DESC
                  LIMIT ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("di", $min_amount, $limit);
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
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['fecha_inicio'])) {
            $where[] = "fecha_fin >= ?";
            $params[] = $filters['fecha_inicio'];
            $types .= 's';
        }
        if (!empty($filters['fecha_fin'])) {
            $where[] = "fecha_fin <= ?";
            $params[] = $filters['fecha_fin'];
            $types .= 's';
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $orderBy = 'fecha_fin';
        $orderDir = 'DESC';
        $allowedSorts = ['maquina_nombre', 'fecha_inicio', 'fecha_fin', 'contador_bn', 'contador_color'];
        if (!empty($filters['sort']) && in_array($filters['sort'], $allowedSorts)) {
            $orderBy = $filters['sort'];
        }
        if (!empty($filters['dir']) && in_array(strtoupper($filters['dir']), ['ASC', 'DESC'])) {
            $orderDir = strtoupper($filters['dir']);
        }
        $query .= " ORDER BY $orderBy $orderDir";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
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

        if (!empty($filters['fecha_inicio'])) {
            $where[] = "fecha_pago >= ?";
            $params[] = $filters['fecha_inicio'];
            $types .= 's';
        }
        if (!empty($filters['fecha_fin'])) {
            $where[] = "fecha_pago <= ?";
            $params[] = $filters['fecha_fin'];
            $types .= 's';
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $orderBy = 'fecha_pago';
        $orderDir = 'DESC';
        $allowedSorts = ['fecha_pago', 'descripcion', 'monto'];
        if (!empty($filters['sort']) && in_array($filters['sort'], $allowedSorts)) {
            $orderBy = $filters['sort'];
        }
        if (!empty($filters['dir']) && in_array(strtoupper($filters['dir']), ['ASC', 'DESC'])) {
            $orderDir = strtoupper($filters['dir']);
        }
        $query .= " ORDER BY $orderBy $orderDir";

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

    public function getSalesDetails($startDate, $endDate, $clientId = null, $orderBy = 'p.fecha_creacion DESC')
    {
        $endDate = $endDate . ' 23:59:59';
        $params = [$startDate, $endDate];
        $types = 'ss';

        $sql = "SELECT
                    p.id as pedido_id,
                    p.fecha_creacion,
                    p.costo_total as total_final,
                    p.estado,
                    (
                        SELECT GROUP_CONCAT(CONCAT_WS(':', metodo_pago, monto, fecha_pago) SEPARATOR ';')
                        FROM pagos
                        WHERE pedido_id = p.id
                        ORDER BY fecha_pago ASC
                    ) as pagos_registrados,
                    SUM(
                        CASE
                            WHEN prod.tipo = 'Servicio' THEN 0
                            WHEN prod.maquina_id = 1 THEN i.cantidad * (0.83 + CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END)
                            WHEN prod.maquina_id = 2 THEN i.cantidad * ((CASE WHEN prod.descripcion LIKE '%Color%' THEN 10.0 ELSE 2.3 END) + (CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END))
                            ELSE 0
                        END
                    ) as costo_pedido
                FROM pedidos p
                LEFT JOIN items_pedido i ON p.id = i.pedido_id
                LEFT JOIN productos prod ON i.producto_id = prod.id
                WHERE p.fecha_creacion BETWEEN ? AND ? AND p.estado = 'Entregado'";

        if ($clientId) {
            $sql .= " AND p.cliente_id = ?";
            $params[] = $clientId;
            $types .= 'i';
        }

        $sql .= " GROUP BY p.id";

        // Validate orderBy to prevent SQL injection
        $allowedOrderBy = ['p.fecha_creacion DESC', 'p.fecha_creacion ASC', 'total_final DESC', 'total_final ASC', 'ganancia DESC', 'ganancia ASC'];
        if (in_array($orderBy, $allowedOrderBy)) {
            if (strpos($orderBy, 'ganancia') !== false) {
                // We need to order by the calculated profit
                $sql .= " ORDER BY (total_final - costo_pedido) " . (strpos($orderBy, 'ASC') ? 'ASC' : 'DESC');
            } else {
                $sql .= " ORDER BY " . $orderBy;
            }
        } else {
            $sql .= " ORDER BY p.fecha_creacion DESC";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $sales = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        // Calculate profit for each sale
        foreach ($sales as &$sale) {
            $sale['ganancia'] = $sale['total_final'] - $sale['costo_pedido'];
        }

        return $sales;
    }

    public function getSalesAndProfitEvolution($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';

        $sql = "SELECT
                    p.id,
                    DATE(p.fecha_creacion) as dia,
                    p.costo_total as venta,
                    COALESCE(p.descuento_total, 0) as descuento,
                    SUM(
                        CASE
                            WHEN prod.tipo = 'Servicio' THEN 0
                            WHEN prod.maquina_id = 1 THEN i.cantidad * (0.83 + CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END)
                            WHEN prod.maquina_id = 2 THEN i.cantidad * ((CASE WHEN prod.descripcion LIKE '%Color%' THEN 10.0 ELSE 2.3 END) + (CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END))
                            ELSE 0
                        END
                    ) as costo_total_pedido
                FROM pedidos p
                LEFT JOIN items_pedido i ON p.id = i.pedido_id
                LEFT JOIN productos prod ON i.producto_id = prod.id
                WHERE p.fecha_creacion BETWEEN ? AND ? AND p.estado = 'Entregado'
                GROUP BY p.id";

        // Check if 'descuento_total' column exists
        $has_descuento = false;
        $result = $this->connection->query("SHOW COLUMNS FROM `pedidos` LIKE 'descuento_total'");
        if ($result && $result->num_rows > 0) {
            $has_descuento = true;
        }

        if (!$has_descuento) {
            // Fallback for old schema: remove discount logic from the main query
            $sql = "SELECT
                        p.id,
                        DATE(p.fecha_creacion) as dia,
                        p.costo_total as venta,
                        0 as descuento, -- Assume no discount
                        SUM(
                            CASE
                                WHEN prod.tipo = 'Servicio' THEN 0
                                WHEN prod.maquina_id = 1 THEN i.cantidad * (0.83 + CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END)
                                WHEN prod.maquina_id = 2 THEN i.cantidad * ((CASE WHEN prod.descripcion LIKE '%Color%' THEN 10.0 ELSE 2.3 END) + (CASE WHEN prod.descripcion LIKE '%A4%' THEN 0.35 ELSE 7.0 END))
                                ELSE 0
                            END
                        ) as costo_total_pedido
                    FROM pedidos p
                    LEFT JOIN items_pedido i ON p.id = i.pedido_id
                    LEFT JOIN productos prod ON i.producto_id = prod.id
                    WHERE p.fecha_creacion BETWEEN ? AND ? AND p.estado = 'Entregado'
                    GROUP BY p.id";
        }


        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $evolutionData = [];
        foreach ($orders as $order) {
            $day = $order['dia'];
            if (!isset($evolutionData[$day])) {
                $evolutionData[$day] = ['dia' => $day, 'total_ventas' => 0, 'total_ganancia' => 0, 'total_costo' => 0];
            }
            $venta_real = $order['venta'] - $order['descuento'];
            $costo_pedido = (float) $order['costo_total_pedido'];
            $evolutionData[$day]['total_ventas'] += $venta_real;
            $evolutionData[$day]['total_ganancia'] += $venta_real - $costo_pedido;
            $evolutionData[$day]['total_costo'] += $costo_pedido;
        }

        // Sort by date
        ksort($evolutionData);

        return array_values($evolutionData);
    }

    public function getTopSellingProductsPaginated($limit, $offset)
    {
        $query = "SELECT
                    prod.id,
                    prod.descripcion,
                    SUM(i.cantidad) as unidades_vendidas,
                    SUM(i.subtotal) as ingresos_generados
                  FROM items_pedido i
                  JOIN productos prod ON i.producto_id = prod.id
                  GROUP BY prod.id, prod.descripcion
                  ORDER BY unidades_vendidas DESC
                  LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countTopSellingProducts()
    {
        $query = "SELECT COUNT(DISTINCT producto_id) as total FROM items_pedido";
        $result = $this->connection->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getLeastSellingProductsPaginated($limit, $offset)
    {
        $query = "SELECT
                    p.id,
                    p.descripcion,
                    IFNULL(SUM(ip.cantidad), 0) as unidades_vendidas
                  FROM productos p
                  LEFT JOIN items_pedido ip ON p.id = ip.producto_id
                  GROUP BY p.id, p.descripcion
                  ORDER BY unidades_vendidas ASC, p.descripcion ASC
                  LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getWeeklyProductionData($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';

        $query = "SELECT
                    p.id as pedido_id,
                    DATE(p.fecha_creacion) as dia,
                    p.costo_total as venta,
                    (
                        SELECT GROUP_CONCAT(CONCAT_WS(':', metodo_pago, monto) SEPARATOR ';')
                        FROM pagos
                        WHERE pedido_id = p.id
                    ) as pagos_registrados,
                    i.cantidad,
                    prod.tipo,
                    prod.descripcion,
                    prod.maquina_id
                  FROM pedidos p
                  JOIN items_pedido i ON p.id = i.pedido_id
                  JOIN productos prod ON i.producto_id = prod.id
                  WHERE p.fecha_creacion BETWEEN ? AND ? AND p.estado = 'Entregado'";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $weeklyData = [];
        $currentDate = new DateTime($startDate);
        $endDateObj = new DateTime($endDate);
        while ($currentDate <= $endDateObj) {
            $dayKey = $currentDate->format('Y-m-d');
            $weeklyData[$dayKey] = [
                'ventas' => 0, 'costos' => 0, 'ganancias' => 0,
                'produccion' => ['bh227_bw' => 0, 'c454e_bw' => 0, 'c454e_color' => 0, 'servicios' => 0],
                'pagos' => ['Efectivo' => 0, 'Débito' => 0, 'Crédito' => 0]
            ];
            $currentDate->modify('+1 day');
        }

        $pedidosProcesados = [];
        foreach ($items as $item) {
            $day = $item['dia'];
            if (!isset($weeklyData[$day])) continue;

            if (!in_array($item['pedido_id'], $pedidosProcesados)) {
                $weeklyData[$day]['ventas'] += (float)$item['venta'];
                if (!empty($item['pagos_registrados'])) {
                    $pagos = explode(';', $item['pagos_registrados']);
                    foreach ($pagos as $pago_str) {
                        list($metodo, $monto) = array_pad(explode(':', $pago_str), 2, null);
                        if (isset($weeklyData[$day]['pagos'][$metodo])) {
                            $weeklyData[$day]['pagos'][$metodo] += (float)$monto;
                        }
                    }
                }
                $pedidosProcesados[] = $item['pedido_id'];
            }

            $costo_item = 0;
            $cantidad = (int)$item['cantidad'];
            if ($item['tipo'] === 'Servicio') {
                $weeklyData[$day]['produccion']['servicios'] += $cantidad;
            } else {
                $costo_papel = (stripos($item['descripcion'], 'A4') !== false) ? 0.35 : 7.0;
                $costo_impresion = 0;
                if ($item['maquina_id'] == 1) { // BH-227
                    $costo_impresion = 0.83;
                    $weeklyData[$day]['produccion']['bh227_bw'] += $cantidad;
                } elseif ($item['maquina_id'] == 2) { // C454e
                    if (stripos($item['descripcion'], 'Color') !== false) {
                        $costo_impresion = 10.0;
                        $weeklyData[$day]['produccion']['c454e_color'] += $cantidad;
                    } else {
                        $costo_impresion = 2.3;
                        $weeklyData[$day]['produccion']['c454e_bw'] += $cantidad;
                    }
                }
                $costo_item = ($costo_impresion + $costo_papel) * $cantidad;
            }
            $weeklyData[$day]['costos'] += $costo_item;
        }

        foreach ($weeklyData as &$data) {
            $data['ganancias'] = $data['ventas'] - $data['costos'];
        }

        return $weeklyData;
    }
    public function getSalesByDayForWeek($startDate, $endDate)
    {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT
                    DAYOFWEEK(fecha_creacion) as day_of_week, -- 1=Sun, 2=Mon,...
                    SUM(costo_total) as total_ventas,
                    COUNT(id) as total_pedidos
                  FROM pedidos
                  WHERE fecha_creacion BETWEEN ? AND ? AND estado = 'Entregado'
                  GROUP BY day_of_week";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Re-key array by day of week (Monday=0, Sunday=6)
        $dataByDay = array_fill(0, 7, ['total_ventas' => 0, 'total_pedidos' => 0]);
        foreach($result as $row) {
            $dayIndex = ($row['day_of_week'] + 5) % 7; // Adjust to make Monday index 0
            $dataByDay[$dayIndex] = [
                'total_ventas' => (float)$row['total_ventas'],
                'total_pedidos' => (int)$row['total_pedidos']
            ];
        }
        return $dataByDay;
    }
    public function getSalesDistributionForDay($date)
    {
        return $this->getSalesDistribution($date, $date);
    }

    public function getAccountBalances()
    {
        $total_pagos_query = "SELECT metodo_pago, SUM(monto) as total FROM pagos GROUP BY metodo_pago";
        $total_pagos_result = $this->connection->query($total_pagos_query);
        $total_pagos = $total_pagos_result ? $total_pagos_result->fetch_all(MYSQLI_ASSOC) : [];

        $total_depositos_query = "SELECT tipo_cuenta, SUM(monto) as total FROM caja_historial GROUP BY tipo_cuenta";
        $total_depositos_result = $this->connection->query($total_depositos_query);
        $total_depositos = $total_depositos_result ? $total_depositos_result->fetch_all(MYSQLI_ASSOC) : [];

        $balances = [
            'efectivo' => 0,
            'banco' => 0
        ];

        foreach ($total_pagos as $pago) {
            if ($pago['metodo_pago'] === 'Efectivo') {
                $balances['efectivo'] += $pago['total'];
            } else {
                $balances['banco'] += $pago['total'];
            }
        }

        foreach ($total_depositos as $deposito) {
            if ($deposito['tipo_cuenta'] === 'efectivo') {
                $balances['efectivo'] -= $deposito['total'];
            } else {
                $balances['banco'] -= $deposito['total'];
            }
        }

        return $balances;
    }

    public function createDeposit($fecha, $tipo_cuenta, $monto, $notas)
    {
        $query = "INSERT INTO caja_historial (fecha, tipo_cuenta, monto, notas) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssds", $fecha, $tipo_cuenta, $monto, $notas);
        return $stmt->execute();
    }

    public function getDepositHistory()
    {
        $query = "SELECT * FROM caja_historial ORDER BY fecha DESC, id DESC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
