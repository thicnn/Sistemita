<?php
class Product
{
    private $connection;
    private $table_name = "productos";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    public function findAllAvailable()
    {
        $query = "SELECT p.*,
                        CASE
                            WHEN p.maquina_id = 1 THEN 'Bh-227'
                            WHEN p.maquina_id = 2 THEN 'C454e'
                            ELSE 'N/A'
                        END as maquina_nombre,
                        (SELECT COUNT(*) FROM items_pedido WHERE descripcion = p.descripcion) as veces_pedido
                  FROM " . $this->table_name . " p
                  WHERE p.disponible = 1
                  ORDER BY p.tipo, p.categoria, p.descripcion";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $descripcion, $precio, $disponible)
    {
        $query = "UPDATE " . $this->table_name . " SET descripcion = ?, precio = ?, disponible = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sdii", $descripcion, $precio, $disponible, $id);
        return $stmt->execute();
    }

    public function create($tipo, $categoria, $descripcion, $precio)
    {
        $query = "INSERT INTO " . $this->table_name . " (tipo, categoria, descripcion, precio, maquina_id) VALUES (?, ?, ?, ?, 1)";
        $stmt = $this->connection->prepare($query);
        $categoria = empty($categoria) ? '' : $categoria;
        $stmt->bind_param("sssd", $tipo, $categoria, $descripcion, $precio);
        return $stmt->execute();
    }

    public function searchAndFilter($filters)
    {
        $query = "SELECT p.*,
                        CASE
                            WHEN p.maquina_id = 1 THEN 'Bh-227'
                            WHEN p.maquina_id = 2 THEN 'C454e'
                            ELSE 'N/A'
                        END as maquina_nombre,
                        (SELECT COUNT(*) FROM items_pedido WHERE descripcion = p.descripcion) as veces_pedido
                  FROM " . $this->table_name . " p";
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['search'])) {
            $where[] = "p.descripcion LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
            $types .= 's';
        }
        if (!empty($filters['tipo'])) {
            $where[] = "p.tipo = ?";
            $params[] = $filters['tipo'];
            $types .= 's';
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }
        $query .= " ORDER BY p.tipo, p.categoria, p.descripcion";

        $stmt = $this->connection->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}