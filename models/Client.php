<?php

class Client
{
    private $connection;
    private $table_name = "clientes";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    /**
     * Busca todos los clientes aplicando los filtros de búsqueda.
     */
    public function findAllWithFilters($filters) {
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

        $query .= " GROUP BY c.id ORDER BY c.nombre ASC";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) { 
            $stmt->bind_param($types, ...$params); 
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Busca un cliente por su ID.
     */
    public function findById($id)
    {
        $query = "SELECT id, nombre, telefono, email, notas FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Crea un nuevo cliente.
     */
    public function create($nombre, $telefono, $email, $notas)
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, telefono, email, notas) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssss", $nombre, $telefono, $email, $notas);
        return $stmt->execute();
    }

    /**
     * Actualiza los datos de un cliente.
     */
    public function update($id, $nombre, $telefono, $email, $notas)
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = ?, telefono = ?, email = ?, notas = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssssi", $nombre, $telefono, $email, $notas, $id);
        return $stmt->execute();
    }
    
    /**
     * Busca clientes por un término para el autocompletado del formulario de pedidos.
     */
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

    /**
     * Cuenta el total de pedidos de un cliente.
     */
    public function countOrders($clientId) {
        $query = "SELECT COUNT(id) as total FROM pedidos WHERE cliente_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * Elimina un cliente por su ID.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Elimina todos los clientes de la base de datos.
     */
    public function deleteAll() {
        $this->connection->query("SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE `clientes`; SET FOREIGN_KEY_CHECKS=1;");
        return true;
    }

    /**
     * Obtiene los 5 mejores clientes por cantidad de pedidos en los últimos 3 meses.
     */
    public function getTopClientsByOrderCount() {
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
    
    /**
     * Cuenta los nuevos clientes registrados en el mes actual.
     */
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
}