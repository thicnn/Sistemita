<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Carga de archivos esenciales
require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/ClientController.php';

// Creación de instancias de los controladores
$authController = new AuthController($connection);
$clientController = new ClientController($connection);

// --- INICIO DEL ENRUTADOR CORREGIDO ---

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Usaremos una serie de if/elseif para manejar las rutas. Es más claro y robusto.

if ($url === 'login') {
    if ($method === 'POST') {
        $authController->handleLogin();
    } else {
        $authController->showLoginForm();
    }
} elseif ($url === 'logout') {
    $authController->logout();

} elseif ($url === 'dashboard' || $url === '') { // Ruta principal o vacía
    if (isset($_SESSION['user_id'])) {
        $authController->showDashboard();
    } else {
        $authController->showLoginForm();
    }
} elseif ($url === 'clients') {
    $clientController->index();

} elseif ($url === 'clients/create') {
    if ($method === 'POST') {
        $clientController->store();
    } else {
        $clientController->showCreateForm();
    }
// Esta es la parte clave que faltaba en tu archivo
} elseif (preg_match('#^clients/edit/(\d+)$#', $url, $matches)) {
    $id = (int)$matches[1]; // Capturamos el ID desde la URL
    if ($method === 'POST') {
        $clientController->update($id);
    } else {
        $clientController->showEditForm($id);
    }
} else {
    // Si ninguna ruta coincide, mostramos un error 404
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Error 404: Página no encontrada</h1>";
    exit();
}