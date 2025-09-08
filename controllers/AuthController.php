<?php
require_once '../models/User.php';
require_once '../models/Order.php';
require_once '../models/Client.php';
require_once '../models/Material.php';
require_once '../models/Config.php';
require_once '../models/Report.php';

class AuthController
{
    private $userModel;
    private $orderModel;
    private $clientModel;
    private $materialModel;
    private $configModel;
    private $reportModel;

    public function __construct($db_connection)
    {
        $this->userModel = new User($db_connection);
        $this->orderModel = new Order($db_connection);
        $this->clientModel = new Client($db_connection);
        $this->materialModel = new Material($db_connection);
        $this->configModel = new Config($db_connection);
        $this->reportModel = new Report($db_connection);
    }

    public function showLoginForm()
    {
        require_once '../views/layouts/header.php';
        require_once '../views/pages/auth/login.php';
        require_once '../views/layouts/footer.php';
    }

    public function showDashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        // --- INICIO DE RECOPILACIÓN DE DATOS ---

        // 1. Datos para colas de producción
        $statusesToTrack = ['En Curso', 'Listo para Retirar'];
        $productionOrders = $this->orderModel->findByStatuses($statusesToTrack);
        $pedidosPorEstado = [];
        foreach ($productionOrders as $pedido) {
            $pedidosPorEstado[$pedido['estado']][] = $pedido;
        }

        // 2. Estadísticas generales
        $dashboardStats = $this->orderModel->getDashboardStats();
        $topClients = $this->clientModel->getTopClientsByOrderCount();
        $lowStockMaterials = $this->materialModel->findMaterialesConStockBajo();

        // 3. Datos para metas mensuales
        $meta_ventas = (float)($this->configModel->findByKey('goal', 'meta_ventas_mensual') ?? 0);
        $meta_pedidos = (int)($this->configModel->findByKey('goal', 'meta_pedidos_mensual') ?? 0);
        $progreso_ventas = $this->reportModel->getMonthlySales();
        $progreso_pedidos = $this->reportModel->getMonthlyOrders();
        $porcentaje_ventas = ($meta_ventas > 0) ? ($progreso_ventas / $meta_ventas) * 100 : 0;
        $porcentaje_pedidos = ($meta_pedidos > 0) ? ($progreso_pedidos / $meta_pedidos) * 100 : 0;
        $goalsData = [
            'sales' => ['goal' => $meta_ventas, 'current' => $progreso_ventas, 'percent' => $porcentaje_ventas],
            'orders' => ['goal' => $meta_pedidos, 'current' => $progreso_pedidos, 'percent' => $porcentaje_pedidos]
        ];

        // 4. Creación del Feed de Actividad Reciente
        $activityFeed = [];

        // Pedidos recientes
        $recentOrders = $this->orderModel->findRecentOrders(5);
        foreach ($recentOrders as $order) {
            $activityFeed[] = [
                'type' => 'pedido',
                'date' => $order['fecha_creacion'],
                'data' => $order
            ];
        }

        // Clientes nuevos
        $recentClients = $this->clientModel->findRecentClients(5);
        foreach ($recentClients as $client) {
            $activityFeed[] = [
                'type' => 'cliente',
                'date' => $client['fecha_creacion'],
                'data' => $client
            ];
        }

        // Pagos importantes
        $importantPayments = $this->reportModel->findRecentImportantPayments(5, 1000); // Umbral de $1000
        foreach ($importantPayments as $payment) {
            $activityFeed[] = [
                'type' => 'pago',
                'date' => $payment['fecha_pago'],
                'data' => $payment
            ];
        }

        // Ordenar el feed por fecha descendente
        usort($activityFeed, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Limitar el feed a un total de 10 items por ejemplo
        $activityFeed = array_slice($activityFeed, 0, 10);

        // --- FIN DE RECOPILACIÓN ---

        require_once '../views/layouts/header.php';
        require_once '../views/pages/dashboard/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /sistemagestion/login');
        exit();
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];
                header('Location: /sistemagestion/dashboard');
                exit();
            } else {
                echo "<h1>Error</h1><p>Credenciales incorrectas. Inténtalo de nuevo.</p>";
                echo '<a href="/sistemagestion/login">Volver</a>';
            }
        }
    }
}