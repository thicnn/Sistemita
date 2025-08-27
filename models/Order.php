<?php

class Order
{
    private $connection;
    private $table_name = "pedidos";
    private $items_table_name = "items_pedido";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    // --- NUEVO MÉTODO PARA CONTAR (CORREGIDO) ---
    public function countAllWithFilters($filters) {
        $query = "SELECT COUNT(DISTINCT p.id) as total 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id";
        
        $where = []; $params = []; $types = '';

        if (!empty($filters['search'])) {
            $where[] = "(c.nombre LIKE ? OR p.id LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            array_push($params, $searchTerm, $searchTerm);
            $types .= 'ss';
        }
        if (!empty($filters['estado'])) {
            $where[] = "p.estado = ?";
            $params[] = $filters['estado'];
            $types .= 's';
        }
        if (!empty($filters['fecha_inicio'])) {
            $where[] = "DATE(p.fecha_creacion) >= ?";
            $params[] = $filters['fecha_inicio'];
            $types .= 's';
        }
        if (!empty($filters['fecha_fin'])) {
            $where[] = "DATE(p.fecha_creacion) <= ?";
            $params[] = $filters['fecha_fin'];
            $types .= 's';
        }

        if (!empty($where)) { $query .= " WHERE " . implode(' AND ', $where); }
        
        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // --- MÉTODO PRINCIPAL DE BÚSQUEDA (CORREGIDO) ---
    public function findAllWithFilters($filters, $limit, $offset)
    {
        $query = "SELECT p.id, p.estado, p.costo_total, p.fecha_creacion, c.nombre as nombre_cliente 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id";
        $where = []; $params = []; $types = '';

        if (!empty($filters['search'])) {
            $where[] = "(c.nombre LIKE ? OR p.id LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            array_push($params, $searchTerm, $searchTerm);
            $types .= 'ss';
        }
        if (!empty($filters['estado'])) {
            $where[] = "p.estado = ?";
            $params[] = $filters['estado'];
            $types .= 's';
        }
        if (!empty($filters['fecha_inicio'])) {
            $where[] = "DATE(p.fecha_creacion) >= ?";
            $params[] = $filters['fecha_inicio'];
            $types .= 's';
        }
        if (!empty($filters['fecha_fin'])) {
            $where[] = "DATE(p.fecha_creacion) <= ?";
            $params[] = $filters['fecha_fin'];
            $types .= 's';
        }

        if (!empty($where)) { $query .= " WHERE " . implode(' AND ', $where); }

        $orderBy = 'p.fecha_creacion'; 
        $orderDir = 'DESC';
        $allowedSorts = ['id', 'nombre_cliente', 'estado', 'costo_total', 'fecha_creacion'];
        if (!empty($filters['sort']) && in_array($filters['sort'], $allowedSorts)) {
            $orderBy = 'p.' . $filters['sort'];
            if ($filters['sort'] === 'nombre_cliente') { $orderBy = 'c.nombre'; }
        }
        if (!empty($filters['dir']) && in_array(strtoupper($filters['dir']), ['ASC', 'DESC'])) {
            $orderDir = strtoupper($filters['dir']);
        }

        $query .= " ORDER BY $orderBy $orderDir LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->connection->prepare($query);
        
        if (!empty($params)) { 
            // Usamos el operador "splat" que es más moderno y seguro
            $stmt->bind_param($types, ...$params); 
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // --- EL RESTO DE MÉTODOS SE MANTIENEN IGUAL ---

    public function findTodaysOrders()
    {
        $hoy = date('Y-m-d');
        $query = "SELECT p.id, p.estado, c.nombre as nombre_cliente 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  WHERE DATE(p.fecha_creacion) = ?
                  ORDER BY p.ultima_actualizacion DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $hoy);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function getDashboardStats()
    {
        $query = "
            SELECT
                (SELECT COUNT(id) FROM pedidos WHERE DATE(fecha_creacion) = CURDATE()) as todays_orders,
                (SELECT SUM(monto) FROM pagos WHERE DATE(fecha_pago) = CURDATE()) as todays_sales
        ";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return [
            'todays_orders' => $result['todays_orders'] ?? 0,
            'todays_sales' => $result['todays_sales'] ?? 0.00,
        ];
    }

    public function findRecentOrders($limit = 5)
    {
        $query = "SELECT p.id, p.estado, p.costo_total, c.nombre as nombre_cliente 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  ORDER BY p.fecha_creacion DESC
                  LIMIT ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function findByStatuses($statuses)
    {
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $query = "SELECT p.id, p.estado, p.costo_total, c.nombre as nombre_cliente 
                  FROM " . $this->table_name . " p
                  LEFT JOIN clientes c ON p.cliente_id = c.id
                  WHERE p.estado IN (" . $placeholders . ")
                  ORDER BY p.ultima_actualizacion ASC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param(str_repeat('s', count($statuses)), ...$statuses);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findByIdWithDetails($id)
    {
        $query_pedido = "SELECT p.*, c.nombre as nombre_cliente FROM " . $this->table_name . " p LEFT JOIN clientes c ON p.cliente_id = c.id WHERE p.id = ?";
        $stmt_pedido = $this->connection->prepare($query_pedido);
        $stmt_pedido->bind_param("i", $id);
        $stmt_pedido->execute();
        $pedido = $stmt_pedido->get_result()->fetch_assoc();

        if (!$pedido) return null;

        $query_items = "SELECT * FROM " . $this->items_table_name . " WHERE pedido_id = ?";
        $stmt_items = $this->connection->prepare($query_items);
        $stmt_items->bind_param("i", $id);
        $stmt_items->execute();
        $pedido['items'] = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

        $query_pagos = "SELECT * FROM pagos WHERE pedido_id = ? ORDER BY fecha_pago ASC";
        $stmt_pagos = $this->connection->prepare($query_pagos);
        $stmt_pagos->bind_param("i", $id);
        $stmt_pagos->execute();
        $pedido['pagos'] = $stmt_pagos->get_result()->fetch_all(MYSQLI_ASSOC);

        return $pedido;
    }

    public function addPayment($pedido_id, $monto, $metodo_pago)
    {
        $query = "INSERT INTO pagos (pedido_id, monto, metodo_pago) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ids", $pedido_id, $monto, $metodo_pago);
        return $stmt->execute();
    }

    public function settlePayment($pedido_id) {
        $order = $this->findByIdWithDetails($pedido_id);
        if (!$order) return false;
        $totalPagado = array_sum(array_column($order['pagos'], 'monto'));
        $saldoPendiente = $order['costo_total'] - $totalPagado;
        if ($saldoPendiente > 0.009) {
            $this->addPayment($pedido_id, $saldoPendiente, 'Efectivo (Automático)');
        }
        return true;
    }

    public function update($id, $estado, $notas, $motivo_cancelacion, $es_interno) {
        if ($motivo_cancelacion !== null) { $estado = 'Cancelado'; }
        $query = "UPDATE " . $this->table_name . " SET estado = ?, notas_internas = ?, motivo_cancelacion = ?, es_interno = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssii", $estado, $notas, $motivo_cancelacion, $es_interno, $id);
        return $stmt->execute();
    }

    public function create($cliente_id, $usuario_id, $estado, $notas, $items, $es_interno)
    {
        $this->connection->begin_transaction();
        try {
            $costo_total_seguro = 0;
            $query_producto = "SELECT precio FROM productos WHERE descripcion = ? LIMIT 1";
            $stmt_producto = $this->connection->prepare($query_producto);
    
            foreach ($items['descripcion'] as $index => $descripcion) {
                $cantidad = (int)$items['cantidad'][$index];
                if (!empty($descripcion) && $cantidad > 0) {
                    $stmt_producto->bind_param("s", $descripcion);
                    $stmt_producto->execute();
                    $resultado = $stmt_producto->get_result()->fetch_assoc();
                    if ($resultado) {
                        $costo_total_seguro += $resultado['precio'] * $cantidad;
                    }
                }
            }
    
            $query_pedido = "INSERT INTO " . $this->table_name . " (cliente_id, usuario_id, estado, notas_internas, costo_total, es_interno) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_pedido = $this->connection->prepare($query_pedido);
            $stmt_pedido->bind_param("iisidi", $cliente_id, $usuario_id, $estado, $notas, $costo_total_seguro, $es_interno);
            $stmt_pedido->execute();
    
            $pedido_id = $this->connection->insert_id;
    
            $query_item = "INSERT INTO " . $this->items_table_name . " (pedido_id, tipo, categoria, descripcion, cantidad, subtotal, doble_faz) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_item = $this->connection->prepare($query_item);

            foreach ($items['descripcion'] as $index => $descripcion) {
                $cantidad = (int)$items['cantidad'][$index];
                if (!empty($descripcion) && $cantidad > 0) {
                    $stmt_producto->bind_param("s", $descripcion);
                    $stmt_producto->execute();
                    $resultado = $stmt_producto->get_result()->fetch_assoc();
                    $subtotal_item = $resultado['precio'] * $cantidad;
                    
                    $tipo_item = $items['tipo'][$index];
                    $categoria = $items['categoria'][$index];
                    $doble_faz = isset($items['doble_faz'][$index]) ? 1 : 0;

                    $stmt_item->bind_param("isssidi", $pedido_id, $tipo_item, $categoria, $descripcion, $cantidad, $subtotal_item, $doble_faz);
                    $stmt_item->execute();
                }
            }
            
            $this->connection->commit();
            return true;
        } catch (Exception $e) {
            $this->connection->rollback();
            error_log("Error al crear pedido: " . $e->getMessage()); 
            return false;
        }
    }

    public function getSalesReport($fechaInicio, $fechaFin)
    {
        $fechaFinCompleta = $fechaFin . ' 23:59:59';
        $query = "SELECT SUM(costo_total) as total_ventas, COUNT(id) as cantidad_pedidos 
                  FROM " . $this->table_name . " 
                  WHERE fecha_creacion BETWEEN ? AND ? AND estado = 'Entregado' AND es_interno = 0";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $fechaInicio, $fechaFinCompleta);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : ['total_ventas' => 0, 'cantidad_pedidos' => 0];
    }
    
    public function findByStatus($status) {
        $query = "SELECT p.*, c.nombre as nombre_cliente FROM pedidos p LEFT JOIN clientes c ON p.cliente_id = c.id WHERE p.estado = ? ORDER BY p.fecha_creacion DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getMonthlySalesComparison($mes1, $mes2) {
        $query = "SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(id) as total_pedidos 
                  FROM pedidos 
                  WHERE estado = 'Entregado' AND es_interno = 0 AND (DATE_FORMAT(fecha_creacion, '%Y-%m') = ? OR DATE_FORMAT(fecha_creacion, '%Y-%m') = ?)
                  GROUP BY mes";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $mes1, $mes2);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findByClientId($clientId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE cliente_id = ? ORDER BY fecha_creacion DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function deleteAll() {
        $this->connection->query("SET FOREIGN_KEY_CHECKS=0;");
        $this->connection->query("TRUNCATE TABLE `pagos`;");
        $this->connection->query("TRUNCATE TABLE `items_pedido`;");
        $this->connection->query("TRUNCATE TABLE `pedidos`;");
        $this->connection->query("SET FOREIGN_KEY_CHECKS=1;");
        return true;
    }
}