<?php
class Report {
    private $connection;

    public function __construct($db_connection) {
        $this->connection = $db_connection;
    }

    public function countOrdersByStatus($startDate, $endDate) {
        $endDate = $endDate . ' 23:59:59';
        $query = "SELECT estado, COUNT(id) as total FROM pedidos WHERE fecha_creacion BETWEEN ? AND ? GROUP BY estado";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }


    public function getProductionCountForPeriod($maquina_id, $tipo, $categoria, $inicio, $fin) {
        $fin_completa = $fin . ' 23:59:59';
        $query = "SELECT SUM(i.cantidad) as total FROM items_pedido i JOIN pedidos p ON i.pedido_id = p.id JOIN productos pr ON i.descripcion = pr.descripcion WHERE pr.maquina_id = ? AND i.tipo = ? AND i.categoria = ? AND p.estado IN ('Listo para Retirar', 'Entregado') AND p.fecha_creacion BETWEEN ? AND ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("issss", $maquina_id, $tipo, $categoria, $inicio, $fin_completa);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function getCounterHistory($filters = []) {
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

    public function saveCounter($maquina, $fecha_inicio, $fecha_fin, $bn, $color, $notas) {
        $query = "INSERT INTO impresora_contadores (maquina_nombre, fecha_inicio, fecha_fin, contador_bn, contador_color, notas) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $color = empty($color) ? 0 : $color;
        $stmt->bind_param("sssiis", $maquina, $fecha_inicio, $fecha_fin, $bn, $color, $notas);
        return $stmt->execute();
    }

    public function getProviderPayments($filters = []) {
        $query = "SELECT * FROM proveedor_pagos";
        $where = []; $params = []; $types = '';
        if (!empty($filters['month'])) {
            $where[] = "DATE_FORMAT(fecha_pago, '%Y-%m') = ?";
            $params[] = $filters['month']; $types .= 's';
        }
        if (!empty($filters['amount']) && is_numeric($filters['amount'])) {
            $where[] = "monto >= ?";
            $params[] = $filters['amount']; $types .= 'd';
        }
        if (!empty($where)) { $query .= " WHERE " . implode(' AND ', $where); }
        $query .= " ORDER BY fecha_pago DESC";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) { $stmt->bind_param($types, ...$params); }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function saveProviderPayment($fecha, $descripcion, $monto) {
        $query = "INSERT INTO proveedor_pagos (fecha_pago, descripcion, monto) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssd", $fecha, $descripcion, $monto);
        return $stmt->execute();
    }

    public function deleteProviderPayment($id) {
        $stmt = $this->connection->prepare("DELETE FROM proveedor_pagos WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteAllCounters() {
        $this->connection->query("TRUNCATE TABLE `impresora_contadores`");
        return true;
    }

    public function deleteAllProviderPayments() {
        $this->connection->query("TRUNCATE TABLE `proveedor_pagos`");
        return true;
    }

    public function deleteCounters($ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->connection->prepare("DELETE FROM impresora_contadores WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        return $stmt->execute();
    }

    public function deleteProviderPayments($ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->connection->prepare("DELETE FROM proveedor_pagos WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        return $stmt->execute();
    }
    
    public function getServicesReport($fechaInicio, $fechaFin) {
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
}