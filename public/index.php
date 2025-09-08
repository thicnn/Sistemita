<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', realpath(__DIR__ . '/..'));

// Carga de archivos esenciales
require_once '../vendor/autoload.php';
require_once '../src/Config/database.php';

use App\Controllers\AuthController;
use App\Controllers\ClientController;
use App\Controllers\OrderController;
use App\Controllers\ReportController;
use App\Controllers\AdminController;
use App\Controllers\ErrorController;

// Creación de instancias de los controladores
$authController = new AuthController($connection);
$clientController = new ClientController($connection);
$orderController = new OrderController($connection);
$reportController = new ReportController($connection);
$adminController = new AdminController($connection);
$errorController = new ErrorController($connection);

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// --- ENRUTADOR REORGANIZADO Y CORREGIDO ---

// Primero, manejamos las rutas dinámicas (las que tienen IDs o parámetros)
if (preg_match('#^clients/show/(\d+)$#', $url, $matches)) {
    $clientController->show((int)$matches[1]);
} elseif (preg_match('#^clients/edit/(\d+)$#', $url, $matches)) {
    ($method === 'POST') ? $clientController->update((int)$matches[1]) : $clientController->showEditForm((int)$matches[1]);
} elseif (preg_match('#^clients/delete/(\d+)$#', $url, $matches)) {
    if ($method === 'POST') $clientController->delete((int)$matches[1]);
} elseif (preg_match('#^clients/check_discount/(\d+)$#', $url, $matches)) {
    $clientController->checkDiscountEligibility((int)$matches[1]);

} elseif (preg_match('#^orders/show/(\d+)$#', $url, $matches)) {
    $orderController->show((int)$matches[1]);
} elseif (preg_match('#^orders/add_payment/(\d+)$#', $url, $matches)) {
    if ($method === 'POST') $orderController->addPayment((int)$matches[1]);
} elseif (preg_match('#^orders/edit/(\d+)$#', $url, $matches)) {
    ($method === 'POST') ? $orderController->update((int)$matches[1]) : $orderController->showEditForm((int)$matches[1]);
} elseif (preg_match('#^admin/products/edit/(\d+)$#', $url, $matches)) {
    ($method === 'POST') ? $adminController->updateProduct((int)$matches[1]) : $adminController->showProductEditForm((int)$matches[1]);
} elseif (preg_match('#^admin/products/delete/(\d+)$#', $url, $matches)) {
    if ($method === 'POST') $adminController->deleteProduct((int)$matches[1]);
} elseif (preg_match('#^reports/status/(.+)$#', $url, $matches)) {
    $reportController->showStatusDetails(urldecode($matches[1]));
} elseif (preg_match('#^reports/delete_payment/(\d+)$#', $url, $matches)) {
    if ($method === 'POST') $reportController->deleteProviderPayment((int)$matches[1]);
} else {
    // Si no es una ruta dinámica, usamos el switch para las rutas estáticas
    switch ($url) {
        // Autenticación y Dashboard
        case 'login':
            ($method === 'POST') ? $authController->handleLogin() : $authController->showLoginForm();
            break;
        case 'logout':
            $authController->logout();
            break;
        case 'dashboard':
        case '':
            $authController->showDashboard();
            break;

        // Clientes
        case 'clients':
            $clientController->index();
            break;
        case 'clients/create':
            ($method === 'POST') ? $clientController->store() : $clientController->showCreateForm();
            break;
        case 'clients/create_ajax':
            if ($method === 'POST') $clientController->createClientAjax();
            break;
        case 'clients/search':
            $clientController->search();
            break;

        // Pedidos
        case 'orders':
            $orderController->index();
            break;
        case 'orders/create':
            ($method === 'POST') ? $orderController->store() : $orderController->showCreateForm();
            break;
        case 'orders/quick_create':
            $orderController->showQuickCreateForm();
            break;
        case 'orders/store_quick':
            if ($method === 'POST') $orderController->storeQuick();
            break;

        // Errores
        case 'errors/create':
            ($method === 'POST') ? $errorController->store() : $errorController->showCreateForm();
            break;

        // Reportes
        case 'reports':
            $reportController->index();
            break;
        case 'reports/dashboard':
            $reportController->dashboard();
            break;
        case 'reports/sales':
            $reportController->sales();
            break;
        case 'reports/weekly_production':
            $reportController->weeklyProduction();
            break;
        case 'reports/weekly_sales_comparison':
            $reportController->weeklySalesComparison();
            break;
        case 'reports/orders':
            $reportController->orders();
            break;
        case 'reports/control':
            $reportController->control();
            break;
        case 'reports/reconciliation':
            $reportController->reconciliation();
            break;
        case 'reports/store_deposit':
            if ($method === 'POST') $reportController->storeDeposit();
            break;
        case 'reports/products':
            $reportController->products();
            break;
        case 'reports/clients':
            $reportController->clients();
            break;
        case 'reports/store_counter':
            if ($method === 'POST') $reportController->storeCounter();
            break;
        case 'reports/store_provider_payment':
            if ($method === 'POST') $reportController->storeProviderPayment();
            break;
        case 'reports/delete_payments':
            if ($method === 'POST') $reportController->deletePayments();
            break;
        case 'reports/delete_counters':
            if ($method === 'POST') $reportController->deleteCounters();
            break;

        // Administración
        case 'admin/settings':
            $adminController->settings();
            break;
        case 'admin/settings/store':
            if ($method === 'POST') $adminController->storeSetting();
            break;
        case 'admin/products':
            $adminController->listProducts();
            break;
        case 'admin/products/create':
            ($method === 'POST') ? $adminController->storeProduct() : $adminController->showProductCreateForm();
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Error 404: Página no encontrada</h1>";
            exit();
    }
}
