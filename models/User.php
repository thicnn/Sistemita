<?php

class User {
    private $connection;
    private $table_name = "usuarios";

    public function __construct($db_connection) {
        $this->connection = $db_connection;
    }

    /**
     * Busca un usuario en la base de datos por su dirección de email.
     * @param string $email El email del usuario a buscar.
     * @return array|null Los datos del usuario si se encuentra, o null si no.
     */
    public function findByEmail($email) {
        $query = "SELECT id, nombre, email, password_hash, rol FROM " . $this->table_name . " WHERE email = ? LIMIT 1";

        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}