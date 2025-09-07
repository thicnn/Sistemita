<?php

class Client
{
    private $connection;
    private $table_name = "clientes";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    public function findAll()
    {
        $query = "SELECT id, nombre, telefono, email FROM " . $this->table_name . " ORDER BY nombre ASC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAllWithFilters($filters)
    {
        $query = "SELECT COUNT(DISTINCT c.id) as total FROM " . $this->table_name . " c";
        $where = [];
        $params = [];
        $types = '';
        if (!empty($filters['search'])) {
            $where[] = "(c.nombre LIKE ? OR c.telefono LIKE ? OR c.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
            $types .= 'sss';
        }
        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function findAllWithFilters($filters, $limit, $offset)
    {
        $query = "SELECT c.*, COUNT(p.id) as total_pedidos 
                  FROM " . $this->table_name . " c
                  LEFT JOIN pedidos p ON c.id = p.cliente_id";
        $where = [];
        $params = [];
        $types = '';
        if (!empty($filters['search'])) {
            $where[] = "(c.nombre LIKE ? OR c.telefono LIKE ? OR c.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
            $types .= 'sss';
        }
        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        $query .= " GROUP BY c.id ORDER BY c.nombre ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById($id)
    {
        $query = "SELECT id, nombre, telefono, email, notas FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function create($nombre, $telefono, $email, $notas)
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, telefono, email, notas) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $email = empty($email) ? null : $email;
        $stmt->bind_param("ssss", $nombre, $telefono, $email, $notas);
        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }
    public function update($id, $nombre, $telefono, $email, $notas)
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = ?, telefono = ?, email = ?, notas = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $email = empty($email) ? null : $email;
        $stmt->bind_param("ssssi", $nombre, $telefono, $email, $notas, $id);
        return $stmt->execute();
    }
    public function searchByTerm($term)
    {
        $likeTerm = "%" . $term . "%";
        $query = "SELECT id, nombre, telefono, email FROM " . $this->table_name . " WHERE nombre LIKE ? OR telefono LIKE ? OR email LIKE ? LIMIT 10";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sss", $likeTerm, $likeTerm, $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function countOrders($clientId)
    {
        $query = "SELECT COUNT(id) as total FROM pedidos WHERE cliente_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getTopClientsByOrderCount()
    {
        $query = "SELECT c.nombre, COUNT(p.id) as total_pedidos 
                  FROM clientes c
                  JOIN pedidos p ON c.id = p.cliente_id
                  WHERE p.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                  GROUP BY c.id, c.nombre
                  ORDER BY total_pedidos DESC
                  LIMIT 5";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countNewClientsThisMonth()
    {
        $month = date('Y-m');
        $query = "SELECT COUNT(id) as total FROM " . $this->table_name . " WHERE DATE_FORMAT(fecha_creacion, '%Y-%m') = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $month);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * Calcula el gasto total de un cliente en el mes actual, contando solo pedidos entregados.
     * @param int $clientId El ID del cliente.
     * @return float El monto total gastado.
     */
    public function getMonthlySpending($clientId)
    {
        $currentMonth = date('Y-m');
        $query = "SELECT SUM(costo_total) as total_gastado 
                  FROM pedidos 
                  WHERE cliente_id = ? 
                  AND DATE_FORMAT(fecha_creacion, '%Y-%m') = ?
                  AND estado = 'Entregado'"; // <-- **LÍNEA CORREGIDA**

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("is", $clientId, $currentMonth);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return (float)($result['total_gastado'] ?? 0);
    }

    /**
     * Verifica si un cliente ya utilizó su descuento en el mes actual.
     * @param int $clientId El ID del cliente.
     * @return bool True si ya lo usó, false si no.
     */
    public function hasUsedMonthlyDiscount($clientId)
    {
        $currentMonth = date('Y-m');
        $query = "SELECT id FROM descuentos_usados WHERE cliente_id = ? AND mes_anio = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("is", $clientId, $currentMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getProductTrendsByClientId($clientId)
    {
        $query = "SELECT
                    prod.descripcion,
                    SUM(ip.cantidad) as total_cantidad,
                    SUM(ip.subtotal) as total_subtotal
                  FROM items_pedido ip
                  JOIN pedidos p ON ip.pedido_id = p.id
                  JOIN productos prod ON ip.producto_id = prod.id
                  WHERE p.cliente_id = ?
                  AND p.estado NOT IN ('Cancelado', 'Cotizacion', 'Solicitud')
                  GROUP BY prod.id, prod.descripcion
                  ORDER BY total_cantidad DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getTopClients($filters)
    {
        $query = "SELECT
                    c.id,
                    c.nombre,
                    COUNT(p.id) as total_pedidos,
                    SUM(p.costo_total) as total_gastado
                  FROM clientes c
                  JOIN pedidos p ON c.id = p.cliente_id
                  WHERE p.estado = 'Entregado'";

        $params = [];
        $types = '';

        if (!empty($filters['fecha_inicio'])) {
            $query .= " AND p.fecha_creacion >= ?";
            $params[] = $filters['fecha_inicio'];
            $types .= 's';
        }
        if (!empty($filters['fecha_fin'])) {
            $query .= " AND p.fecha_creacion <= ?";
            $params[] = $filters['fecha_fin'] . ' 23:59:59';
            $types .= 's';
        }

        $query .= " GROUP BY c.id, c.nombre";

        $orderBy = 'total_gastado';
        if (!empty($filters['sort']) && in_array($filters['sort'], ['total_pedidos', 'total_gastado'])) {
            $orderBy = $filters['sort'];
        }
        $query .= " ORDER BY $orderBy DESC";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function getRFMData()
    {
        $query = "SELECT
                    c.id,
                    c.nombre,
                    DATEDIFF(NOW(), MAX(p.fecha_creacion)) as recencia,
                    COUNT(p.id) as frecuencia,
                    SUM(p.costo_total) as monetario
                  FROM clientes c
                  JOIN pedidos p ON c.id = p.cliente_id
                  WHERE p.estado = 'Entregado'
                  GROUP BY c.id, c.nombre";

        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}