<?php

class Material
{
    private $connection;
    private $table_name = "materiales";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    public function findAll()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nombre ASC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function findById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida)
    {
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, stock_actual, stock_minimo, unidad_medida) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssdds", $nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida);
        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }

    public function update($id, $nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida)
    {
        $query = "UPDATE " . $this->table_name . " SET nombre = ?, descripcion = ?, stock_actual = ?, stock_minimo = ?, unidad_medida = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssddsi", $nombre, $descripcion, $stock_actual, $stock_minimo, $unidad_medida, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function reducirStock($material_id, $cantidad)
    {
        $query = "UPDATE " . $this->table_name . " SET stock_actual = stock_actual - ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("di", $cantidad, $material_id);
        return $stmt->execute();
    }

    public function findMaterialesConStockBajo()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE stock_actual <= stock_minimo AND stock_minimo > 0 ORDER BY nombre ASC";
        $result = $this->connection->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
