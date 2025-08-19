<?php
require_once '../models/User.php';
require_once '../models/Order.php';

class AuthController
{
    private $userModel;
    private $orderModel;

    public function __construct($db_connection)
    {
        $this->userModel = new User($db_connection);
        $this->orderModel = new Order($db_connection);
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

        // 1. Obtenemos todos los pedidos del día de hoy
        $todaysOrders = $this->orderModel->findTodaysOrders();

        // 2. Los organizamos en listas separadas por su estado
        $pedidosPorEstado = [];
        foreach ($todaysOrders as $pedido) {
            $pedidosPorEstado[$pedido['estado']][] = $pedido;
        }

        // 3. Pasamos la lista organizada a la vista
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