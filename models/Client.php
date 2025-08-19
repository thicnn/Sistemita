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
     * Busca todos los clientes aplicando los filtros proporcionados.
     */
    public function findAllWithFilters($filters) {
        $query = "SELECT * FROM " . $this->table_name;
        $where = []; 
        $params = []; 
        $types = '';

        if (!empty($filters['search'])) {
            $where[] = "(nombre LIKE ? OR telefono LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            array_push($params, $searchTerm, $searchTerm, $searchTerm);
            $types .= 'sss';
        }
        if (!empty($filters['fecha'])) {
            $where[] = "DATE(fecha_creacion) = ?";
            $params[] = $filters['fecha'];
            $types .= 's';
        }

        if (!empty($where)) { 
            $query .= " WHERE " . implode(' AND ', $where); 
        }
        $query .= " ORDER BY nombre ASC";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) { 
            $stmt->bind_param($types, ...$params); 
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Cuenta el total de pedidos históricos de un cliente.
     */
    public function countOrders($clientId) {
        $query = "SELECT COUNT(id) as total FROM pedidos WHERE cliente_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function findAll()
    {
        $query = "SELECT id, nombre, telefono, email, notas FROM " . $this->table_name . " ORDER BY nombre ASC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function findById($id)
    {
        $query = "SELECT id, nombre, telefono, email, notas FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function create($nombre, $telefono, $email, $notas)
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, telefono, email, notas) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $nombre = htmlspecialchars(strip_tags($nombre));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $email = htmlspecialchars(strip_tags($email));
        $notas = htmlspecialchars(strip_tags($notas));
        $stmt->bind_param("ssss", $nombre, $telefono, $email, $notas);
        return $stmt->execute();
    }

    public function update($id, $nombre, $telefono, $email, $notas)
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = ?, telefono = ?, email = ?, notas = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $nombre = htmlspecialchars(strip_tags($nombre));
        $telefono = htmlspecialchars(strip_tags($telefono));
        $email = htmlspecialchars(strip_tags($email));
        $notas = htmlspecialchars(strip_tags($notas));
        $stmt->bind_param("ssssi", $nombre, $telefono, $email, $notas, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deleteAll() {
        $this->connection->query("SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE `clientes`; SET FOREIGN_KEY_CHECKS=1;");
        return true;
    }

    public function searchByTerm($term)
    {
        $likeTerm = "%" . $term . "%";
        $query = "SELECT id, nombre, telefono, email FROM " . $this->table_name . " WHERE telefono LIKE ? OR email LIKE ? LIMIT 10";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $likeTerm, $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}