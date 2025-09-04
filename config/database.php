<?php

// Fichero de configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'centro_impresion');

// --- LLAVE DE ENCRIPTACIÓN (CORREGIDA Y FINAL) ---
// Esta clave tiene exactamente 32 caracteres, que es lo requerido.
define('ENCRYPTION_KEY', 'EstaClaveSecretaTiene32Caracteres');

// --- Proceso de Conexión ---
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Error de Conexión: " . $connection->connect_error);
}

if (!$connection->set_charset("utf8")) {
    printf("Error cargando el conjunto de caracteres utf8: %s\n", $connection->error);
    exit();
}
?>