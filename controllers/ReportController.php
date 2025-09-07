<?php
require_once '../models/Order.php';
require_once '../models/Report.php';
require_once '../models/Client.php';
require_once '../models/Product.php';

class ReportController
{
    private $orderModel;
    private $reportModel;
    private $clientModel;
    private $productModel;

    public function __construct($db_connection)
    {
        $this->orderModel = new Order($db_connection);
        $this->reportModel = new Report($db_connection);
        $this->clientModel = new Client($db_connection);
        $this->productModel = new Product($db_connection);
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
        $salesOverTime = $this->reportModel->getSalesOverTime();
        $topProducts = $this->reportModel->getTopSellingProducts($startDate, $endDate);
        $printerCounters = $this->reportModel->getPrinterCounters($selectedMonth);

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

    public function dashboard()
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
        $profitOverTime = $this->reportModel->getProfitOverTime();
        $topProducts = $this->reportModel->getTopSellingProducts($startDate, $endDate);

        $totalLosses = $this->reportModel->getLosses($startDate, $endDate);
        // --- FIN DE NUEVAS LLAMADAS ---

        // $c454e_bn_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'blanco y negro', $startDate, $endDate);
        // $c454e_color_prod = $this->reportModel->getProductionCountForPeriod(2, 'Impresion', 'color', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(2, 'Fotocopia', 'color', $startDate, $endDate);
        // $bh227_total_prod = $this->reportModel->getProductionCountForPeriod(1, 'Impresion', 'blanco y negro', $startDate, $endDate) + $this->reportModel->getProductionCountForPeriod(1, 'Fotocopia', 'blanco y negro', $startDate, $endDate);

        // --- Nuevos reportes de Ganancia y Contadores ---
        $profitStartDate = $_GET['profit_start'] ?? date('Y-m-01');
        $profitEndDate = $_GET['profit_end'] ?? date('Y-m-t');
        $profitReport = $this->reportModel->getProfitByDateRange($profitStartDate, $profitEndDate);

        // Para el nuevo reporte de Distribución de Ventas, usamos el filtro principal
        $salesDistribution = $this->reportModel->getSalesDistribution($startDate, $endDate);

        $countersMonth = $_GET['counters_month'] ?? date('Y-m');
        $printerCounters = $this->reportModel->getPrinterCounters($countersMonth);
        // --- Fin de nuevos reportes ---

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
        require_once '../views/pages/reports/dashboard.php';
        require_once '../views/layouts/footer.php';
    }

    public function sales()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $clientId = $_GET['client_id'] ?? null;
        $orderBy = $_GET['order_by'] ?? 'p.fecha_creacion DESC';

        $sales = $this->reportModel->getSalesDetails($startDate, $endDate, $clientId, $orderBy);
        $clients = $this->clientModel->findAll();
        $evolution = $this->reportModel->getSalesAndProfitEvolution($startDate, $endDate);
        $losses = $this->reportModel->getLosses($startDate, $endDate);
        $salesDistribution = $this->reportModel->getSalesDistribution($startDate, $endDate);
        $profitReport = $this->reportModel->getProfitByDateRange($startDate, $endDate);


        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/sales.php';
        require_once '../views/layouts/footer.php';
    }

    public function weeklyProduction()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        // Handle date selection
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $dateTime = new DateTime($selectedDate);
        $dayOfWeek = $dateTime->format('N'); // 1 (for Monday) through 7 (for Sunday)

        // Calculate Monday of the selected week
        $startOfWeek = clone $dateTime;
        $startOfWeek->modify('-' . ($dayOfWeek - 1) . ' days');

        // Calculate Sunday of the selected week
        $endOfWeek = clone $startOfWeek;
        $endOfWeek->modify('+6 days');

        $startDate = $startOfWeek->format('Y-m-d');
        $endDate = $endOfWeek->format('Y-m-d');

        $weeklyData = $this->reportModel->getWeeklyProductionData($startDate, $endDate);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/weekly_production.php';
        require_once '../views/layouts/footer.php';
    }

    public function orders()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        $filters = [
            'search' => $_GET['search'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'sort' => $_GET['sort'] ?? 'fecha_creacion',
            'dir' => $_GET['dir'] ?? 'DESC',
        ];

        $page = (int)($_GET['page'] ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $totalOrders = $this->orderModel->countAllWithFilters($filters);
        $orders = $this->orderModel->findAllWithFilters($filters, $limit, $offset);

        $totalPages = ceil($totalOrders / $limit);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/orders.php';
        require_once '../views/layouts/footer.php';
    }

    public function control()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        // General date filter for the page
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        // Production Control Orders
        $productionFilters = [
            'estado' => ['Listo para Retirar', 'Entregado'],
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'sort' => $_GET['prod_sort'] ?? 'fecha_creacion',
            'dir' => $_GET['prod_dir'] ?? 'DESC',
        ];
        $productionOrders = $this->orderModel->findAllWithFilters($productionFilters, 1000, 0); // High limit to get all

        // Provider Payments
        $paymentFilters = [
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'sort' => $_GET['payment_sort'] ?? 'fecha_pago',
            'dir' => $_GET['payment_dir'] ?? 'DESC',
        ];
        $providerPayments = $this->reportModel->getProviderPayments($paymentFilters);

        // Counter History
        $counterFilters = [
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'sort' => $_GET['counter_sort'] ?? 'fecha_fin',
            'dir' => $_GET['counter_dir'] ?? 'DESC',
        ];
        $counterHistory = $this->reportModel->getCounterHistory($counterFilters);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/control.php';
        require_once '../views/layouts/footer.php';
    }

    public function products()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        // Item Search (now with filters)
        $productFilters = [
            'tipo' => $_GET['tipo'] ?? '',
            'maquina_id' => $_GET['maquina_id'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];
        $allProducts = $this->productModel->searchAndFilter($productFilters);

        // Data for filters
        $tipos = $this->productModel->getDistinctTipos();
        $maquinas = $this->productModel->getDistinctMaquinas();
        $categorias = $this->productModel->getDistinctCategorias();

        $selectedProductId = $_GET['product_id'] ?? null;
        $ordersByProduct = [];
        if ($selectedProductId) {
            $ordersByProduct = $this->orderModel->findOrdersByProductId($selectedProductId);
        }

        // Top Selling Products
        $topSellingPage = (int)($_GET['top_page'] ?? 1);
        $limit = 25;
        $topSellingOffset = ($topSellingPage - 1) * $limit;
        $totalTopSelling = $this->reportModel->countTopSellingProducts();
        $topSellingProducts = $this->reportModel->getTopSellingProductsPaginated($limit, $topSellingOffset);
        $topSellingTotalPages = ceil($totalTopSelling / $limit);

        // Least Selling Products
        $leastSellingPage = (int)($_GET['least_page'] ?? 1);
        $leastSellingOffset = ($leastSellingPage - 1) * $limit;
        $totalLeastSelling = $this->productModel->countAll();
        $leastSellingProducts = $this->reportModel->getLeastSellingProductsPaginated($limit, $leastSellingOffset);
        $leastSellingTotalPages = ceil($totalLeastSelling / $limit);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/products.php';
        require_once '../views/layouts/footer.php';
    }

    public function clients()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        // Client Search and Trends
        $allClients = $this->clientModel->findAll();
        $selectedClientId = $_GET['client_id'] ?? null;
        $productTrends = [];
        if ($selectedClientId) {
            $productTrends = $this->clientModel->getProductTrendsByClientId($selectedClientId);
        }

        // Top Clients
        $topClientsFilters = [
            'fecha_inicio' => $_GET['start_date'] ?? '',
            'fecha_fin' => $_GET['end_date'] ?? '',
            'sort' => $_GET['sort'] ?? 'total_gastado',
        ];
        $topClients = $this->clientModel->getTopClients($topClientsFilters);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/reports/clients.php';
        require_once '../views/layouts/footer.php';
    }


    public function showStatusDetails($status)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }

        $filters = [
            'search' => $_GET['search'] ?? '',
            'estado' => $status, // Set the status from the URL
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'sort' => $_GET['sort'] ?? 'fecha_creacion',
            'dir' => $_GET['dir'] ?? 'DESC',
        ];

        $page = (int)($_GET['page'] ?? 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $totalOrders = $this->orderModel->countAllWithFilters($filters);
        $orders = $this->orderModel->findAllWithFilters($filters, $limit, $offset);

        $totalPages = ceil($totalOrders / $limit);

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
        header('Location: /sistemagestion/reports/control');
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
        header('Location: /sistemagestion/reports/control');
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
