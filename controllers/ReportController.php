<?php
require_once '../models/Order.php';
require_once '../models/Report.php';

class ReportController
{
    private $orderModel;
    private $reportModel;

    public function __construct($db_connection)
    {
        $this->orderModel = new Order($db_connection);
        $this->reportModel = new Report($db_connection);
    }

    public function index()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        // Lógica de fechas
        $selectedMonth = $_GET['month'] ?? date('Y-m');
        $startDate = $selectedMonth . '-01';
        $endDate = date("Y-m-t", strtotime($startDate));

        $paymentFilters = ['month' => $_GET['payment_month'] ?? ''];
        $counterFilters = ['month' => $_GET['counter_month'] ?? ''];

        // Llamadas a los modelos
        $statusCounts = $this->reportModel->countOrdersByStatus($startDate, $endDate);
        $providerPayments = $this->reportModel->getProviderPayments($paymentFilters);
        $counterHistory = $this->reportModel->getCounterHistory($counterFilters);

        // --- NUEVAS LLAMADAS A LOS MÉTODOS PARA LOS GRÁFICOS ---
        $salesOverTime = $this->reportModel->getSalesOverTime();
        $topProducts = $this->reportModel->getTopSellingProducts($startDate, $endDate);

        $selectedYear = $_GET['year'] ?? date('Y');
        $newClientsData = $this->reportModel->getNewClientsPerMonth($selectedYear);
        $totalLosses = $this->reportModel->getLosses($startDate, $endDate);
        // --- FIN DE NUEVAS LLAMADAS ---

        $c454e_bn_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'blanco y negro', $startDate, $endDate);
        $c454e_color_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'color', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'color', $startDate, $endDate);
        $bh227_total_prod = $this->reportModel->getProductionCountForPeriod(1, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(1, 'Fotocopia', 'blanco y negro', $startDate, $endDate);

        // Preparamos los datos para los gráficos
        $chartLabels = [];
        $chartData = [];
        if (isset($statusCounts) && is_array($statusCounts)) {
            foreach ($statusCounts as $status) {
                $chartLabels[] = $status['estado'];
                $chartData[] = $status['total'];
            }
        }

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/index.php';
        require_once '../views/layouts/footer.php';
    }


    public function showStatusDetails($status)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        $orders = $this->orderModel->findByStatus($status);
        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/status_details.php';
        require_once '../views/layouts/footer.php';
    }

    public function storeCounter()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            exit('Acceso denegado');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reportModel->saveCounter($_POST['maquina'], $_POST['fecha_inicio'], $_POST['fecha_fin'], $_POST['contador_bn'], $_POST['contador_color'] ?? 0, $_POST['notas'] ?? '');
            $_SESSION['toast'] = ['message' => 'Contador registrado con éxito.', 'type' => 'success'];
        }
        header('Location: /sistemagestion/reports');
        exit();
    }

    public function storeProviderPayment()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            exit('Acceso denegado');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reportModel->saveProviderPayment($_POST['fecha_pago'], $_POST['descripcion'], $_POST['monto']);
            $_SESSION['toast'] = ['message' => 'Pago a proveedor registrado.', 'type' => 'success'];
        }
        header('Location: /sistemagestion/reports');
        exit();
    }

    public function deletePayments()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            exit(json_encode(['success' => false, 'message' => 'Acceso denegado']));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $success = $this->reportModel->deleteProviderPayments($_POST['ids']);
            $_SESSION['toast'] = ['message' => 'Pagos eliminados.', 'type' => 'danger'];
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Petición inválida']);
        exit();
    }

    public function deleteCounters()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            exit(json_encode(['success' => false, 'message' => 'Acceso denegado']));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids'])) {
            $success = $this->reportModel->deleteCounters($_POST['ids']);
            $_SESSION['toast'] = ['message' => 'Registros de contador eliminados.', 'type' => 'danger'];
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Petición inválida']);
        exit();
    }

    public function deleteProviderPayment($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/reports');
            exit('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->reportModel->deleteProviderPayment($id);
            if ($success) {
                $_SESSION['toast'] = ['message' => 'Pago eliminado con éxito.', 'type' => 'success'];
            } else {
                $_SESSION['toast'] = ['message' => 'Error al eliminar el pago.', 'type' => 'danger'];
            }
        }

        header('Location: /sistemagestion/reports');
        exit();
    }
}
