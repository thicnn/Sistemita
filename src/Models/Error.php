<?php
namespace App\Models;

class Error
{
    private $connection;
    private $table_name = "pedidos_errores";

    public function __construct($db_connection)
    {
        $this->connection = $db_connection;
    }

    public function create($tipo_error, $cantidad, $costo_total, $usuario_id)
    {
        $query = "INSERT INTO " . $this->table_name . " (tipo_error, cantidad, costo_total, usuario_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sidi", $tipo_error, $cantidad, $costo_total, $usuario_id);
        return $stmt->execute();
    }
}
