<?php

class Config {
    private $connection;
    private $table_name = "configuracion";

    public function __construct($db_connection) {
        $this->connection = $db_connection;
    }

    /**
     * Busca todas las configuraciones de un tipo específico (ej: todos los 'papel').
     * @param string $tipo El tipo de configuración a buscar.
     * @return array La lista de configuraciones encontradas.
     */
    public function findByType($tipo) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE tipo = ? ORDER BY nombre ASC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $tipo);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Añade una nueva configuración a la base de datos.
     * @param string $tipo (ej: 'papel', 'acabado')
     * @param string $nombre (ej: 'Ilustración 150g')
     * @param string $valor (ej: '25.50')
     * @return bool True si fue exitoso, false si falló.
     */
    public function create($tipo, $nombre, $valor) {
        $query = "INSERT INTO " . $this->table_name . " (tipo, nombre, valor) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssd", $tipo, $nombre, $valor); // 'd' para valores decimales como el precio
        return $stmt->execute();
    }
}