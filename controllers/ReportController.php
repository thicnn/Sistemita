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

        // Recoge los filtros de la URL o establece valores por defecto
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $mes1 = $_GET['mes1'] ?? date('Y-m');
        $mes2 = $_GET['mes2'] ?? date('Y-m', strtotime('-1 month'));
        $paymentFilters = ['month' => $_GET['payment_month'] ?? '', 'amount' => $_GET['payment_amount'] ?? ''];
        $counterFilters = ['month' => $_GET['counter_month'] ?? ''];
        
        // Pide todos los datos necesarios a los modelos
        $salesData = $this->orderModel->getSalesReport($fechaInicio, $fechaFin);
        $statusCounts = $this->reportModel->countOrdersByStatus();
        $monthlyComparison = $this->orderModel->getMonthlySalesComparison($mes1, $mes2);
        $providerPayments = $this->reportModel->getProviderPayments($paymentFilters);
        $counterHistory = $this->reportModel->getCounterHistory($counterFilters);
        $servicesReport = $this->reportModel->getServicesReport($fechaInicio, $fechaFin);

        // Calcula la producción del mes actual
        $primerDiaMes = date('Y-m-01');
        $hoy = date('Y-m-d');
        $c454e_bn_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'blanco y negro', $primerDiaMes, $hoy) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'blanco y negro', $primerDiaMes, $hoy);
        $c454e_color_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'color', $primerDiaMes, $hoy) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'color', $primerDiaMes, $hoy);
        $bh227_total_prod = $this->reportModel->getProductionCountForPeriod(1, 'Impresion', 'blanco y negro', $primerDiaMes, $hoy) + $this->reportModel->getProductionCountForPeriod(1, 'Fotocopia', 'blanco y negro', $primerDiaMes, $hoy);
        
        // Carga la vista con todos los datos
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
        header('Location: /sistemagestion/reports'); exit();
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