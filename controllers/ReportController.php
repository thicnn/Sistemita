<?php
require_once '../models/Order.php';
require_once '../models/Report.php';

class ReportController {
    private $orderModel;
    private $reportModel;

    public function __construct($db_connection) {
        $this->orderModel = new Order($db_connection);
        $this->reportModel = new Report($db_connection);
    }

    public function index() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { 
            header('Location: /sistemagestion/dashboard'); 
            exit(); 
        }

        // --- LÓGICA DE FECHAS MEJORADA ---
        $selectedMonth = $_GET['month'] ?? date('Y-m');
        $startDate = $selectedMonth . '-01';
        $endDate = date("Y-m-t", strtotime($startDate));

        $paymentFilters = ['month' => $_GET['payment_month'] ?? ''];
        $counterFilters = ['month' => $_GET['counter_month'] ?? ''];

        // --- LLAMADAS A LOS MODELOS CON LOS DATOS CORRECTOS ---
        $salesData = $this->orderModel->getSalesReport($startDate, $endDate);
        $statusCounts = $this->reportModel->countOrdersByStatus($startDate, $endDate); 
        $providerPayments = $this->reportModel->getProviderPayments($paymentFilters);
        $counterHistory = $this->reportModel->getCounterHistory($counterFilters);
        $servicesReport = $this->reportModel->getServicesReport($startDate, $endDate);

        // ¡CORREGIDO! La producción ahora se calcula para el mes seleccionado
        $c454e_bn_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'blanco y negro', $startDate, $endDate);
        $c454e_color_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'color', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'color', $startDate, $endDate);
        $bh227_total_prod = $this->reportModel->getProductionCountForPeriod(1, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(1, 'Fotocopia', 'blanco y negro', $startDate, $endDate);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/index.php';
        require_once '../views/layouts/footer.php';
    }


    public function showStatusDetails($status) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        $orders = $this->orderModel->findByStatus($status);
        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/status_details.php';
        require_once '../views/layouts/footer.php';
    }

    public function storeCounter() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reportModel->saveCounter($_POST['maquina'], $_POST['fecha_inicio'], $_POST['fecha_fin'], $_POST['contador_bn'], $_POST['contador_color'] ?? 0, $_POST['notas'] ?? '');
        }
        header('Location: /sistemagestion/reports'); exit();
    }

    public function storeProviderPayment() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { exit('Acceso denegado'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reportModel->saveProviderPayment($_POST['fecha_pago'], $_POST['descripcion'], $_POST['monto']);
        }
        header('Location: /sistemagestion/reports'); 
        exit(); // --- LÍNEA AÑADIDA PARA MÁXIMA SEGURIDAD ---
    }
    
    public function deleteProviderPayment($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reportModel->deleteProviderPayment($id);
        }
        header('Location: /sistemagestion/reports');
        exit();
    }
    
    public function deletePayments() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { exit(json_encode(['success' => false, 'message' => 'Acceso denegado'])); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $success = $this->reportModel->deleteProviderPayments($_POST['ids']);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Petición inválida']);
        exit();
    }

    public function deleteCounters() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { exit(json_encode(['success' => false, 'message' => 'Acceso denegado'])); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $success = $this->reportModel->deleteCounters($_POST['ids']);
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Petición inválida']);
        exit();
    }
}