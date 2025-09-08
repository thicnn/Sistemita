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

    public function findByKey($tipo, $nombre) {
        $query = "SELECT valor FROM " . $this->table_name . " WHERE tipo = ? AND nombre = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ss", $tipo, $nombre);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['valor'] ?? null;
    }

    public function updateOrCreate($tipo, $nombre, $valor) {
        // Aprovecha la clave única (tipo, nombre) para actualizar si existe, o insertar si no.
        $query = "INSERT INTO " . $this->table_name . " (tipo, nombre, valor) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sss", $tipo, $nombre, $valor); // Usamos 's' para valor para más flexibilidad
        return $stmt->execute();
    }
}