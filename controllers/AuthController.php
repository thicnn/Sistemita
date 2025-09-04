<?php
require_once '../models/User.php';
require_once '../models/Order.php';
require_once '../models/Client.php'; // ¡Necesitamos el ClientModel!

class AuthController
{
    private $userModel;
    private $orderModel;

    private $clientModel; // Añadimos la propiedad
    public function __construct($db_connection)
    {
        $this->userModel = new User($db_connection);
        $this->orderModel = new Order($db_connection);
        $this->clientModel = new Client($db_connection); // Lo instanciamos
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

        // 1. Obtener estados para las colas de producción
        $statusesToTrack = ['En Curso', 'Listo para Retirar'];
        $productionOrders = $this->orderModel->findByStatuses($statusesToTrack);
        
        $pedidosPorEstado = [];
        foreach ($productionOrders as $pedido) {
            $pedidosPorEstado[$pedido['estado']][] = $pedido;
        }

        // 2. Obtener nuevas estadísticas
        $dashboardStats = $this->orderModel->getDashboardStats();
        $newClientsThisMonth = $this->clientModel->countNewClientsThisMonth();

        // 3. Obtener pedidos recientes
        $recentOrders = $this->orderModel->findRecentOrders(5);

        // 4. Obtener el ranking de mejores clientes
        $topClients = $this->clientModel->getTopClientsByOrderCount();

        // 5. Pasamos todos los datos a la vista
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