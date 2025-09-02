<?php
require_once '../models/Client.php';
require_once '../models/Order.php';

class ClientController
{
    private $clientModel;
    private $orderModel;

    public function __construct($db_connection)
    {
        $this->clientModel = new Client($db_connection);
        $this->orderModel = new Order($db_connection);
    }

    public function show($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $client = $this->clientModel->findById($id);

        if (!$client) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Error 404: Cliente no encontrado</h1>";
            exit();
        }

        // --- LÓGICA DE DESCUENTO MENSUAL (ya estaba correcta) ---
        // $gastoMensual = $this->clientModel->getMonthlySpending($id);
        // $yaUsoDescuento = $this->clientModel->hasUsedMonthlyDiscount($id);
        // $metaDescuento = 300;

        // $descuentoInfo = [
        //     'disponible' => false,
        //     'usado' => $yaUsoDescuento,
        //     'gasto_actual' => $gastoMensual,
        //     'restante' => max(0, $metaDescuento - $gastoMensual),
        //     'meta' => $metaDescuento,
        //     'monto_descuento' => 0,
        //     'progreso_pct' => ($gastoMensual / $metaDescuento) * 100
        // ];

        // if ($gastoMensual > $metaDescuento && !$yaUsoDescuento) {
        //     $descuentoInfo['disponible'] = true;
        //     $descuentoInfo['monto_descuento'] = $gastoMensual * 0.10;
        // }
        $descuentoInfo = null;

        // --- CÁLCULO DE ESTADÍSTICAS GLOBALES (CORREGIDO) ---
        $client['pedidos'] = $this->orderModel->findByClientId($id);

        // 1. Filtramos solo los pedidos entregados para el cálculo
        $pedidosEntregados = array_filter($client['pedidos'], function ($pedido) {
            return $pedido['estado'] === 'Entregado';
        });

        // 2. Sumamos el costo total solo de los pedidos filtrados
        $client['total_gastado'] = array_sum(array_column($pedidosEntregados, 'costo_total'));

        // 3. El total de pedidos sí puede incluir todos los estados
        $client['total_pedidos'] = count($client['pedidos']);
        // --- FIN DE LA CORRECCIÓN ---


        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/show.php';
        require_once '../views/layouts/footer.php';
    }

    // ... (El resto de los métodos del controlador no necesitan cambios)

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }

        $filters = ['search' => $_GET['search'] ?? ''];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $perPage = 6;

        $totalClients = $this->clientModel->countAllWithFilters($filters);
        $totalPages = ceil($totalClients / $perPage);
        $offset = ($page - 1) * $perPage;

        $clients = $this->clientModel->findAllWithFilters($filters, $perPage, $offset);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /sistemagestion/login');
            exit();
        }
        $this->clientModel->create($_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['notas']);

        $_SESSION['toast'] = ['message' => '¡Cliente creado con éxito!', 'type' => 'success'];

        header('Location: /sistemagestion/clients');
        exit();
    }

    public function showEditForm($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/clients');
            exit();
        }
        $client = $this->clientModel->findById($id);
        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/edit.php';
        require_once '../views/layouts/footer.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/clients');
            exit();
        }
        $this->clientModel->update($id, $_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['notas']);

        $_SESSION['toast'] = ['message' => '¡Cliente actualizado correctamente!', 'type' => 'info'];

        header('Location: /sistemagestion/clients');
        exit();
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/clients');
            exit();
        }
        $this->clientModel->delete($id);

        $_SESSION['toast'] = ['message' => 'Cliente eliminado.', 'type' => 'danger'];

        header('Location: /sistemagestion/clients');
        exit();
    }

    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['term'])) {
            $clients = $this->clientModel->searchByTerm($_GET['term']);
            header('Content-Type: application/json');
            echo json_encode($clients);
            exit();
        }
    }

    public function checkDiscountEligibility($id)
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 403 Forbidden');
            exit();
        }

        $montoGastado = $this->clientModel->getMonthlySpending($id);
        $yaUsoDescuento = $this->clientModel->hasUsedMonthlyDiscount($id);

        $response = [
            'eligible' => false,
            'spent' => $montoGastado,
            'already_used' => $yaUsoDescuento
        ];

        if ($montoGastado > 300 && !$yaUsoDescuento) {
            $response['eligible'] = true;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    public function createClientAjax()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            exit();
        }

        $nombre = $_POST['nombre'] ?? '';
        if (empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre del cliente es obligatorio.']);
            exit();
        }

        $telefono = $_POST['telefono'] ?? '';
        $email = $_POST['email'] ?? '';
        $notas = $_POST['notas'] ?? '';

        // Utiliza el método de modelo existente, que ya maneja emails vacíos
        $success = $this->clientModel->create($nombre, $telefono, $email, $notas);

        if ($success) {
            $newClientId = $this->clientModel->connection->insert_id;
            $newClient = $this->clientModel->findById($newClientId);
            echo json_encode(['success' => true, 'client' => $newClient]);
        } else {
            // Intenta detectar el error de email duplicado
            if ($this->clientModel->connection->errno === 1062) {
                 echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está registrado.']);
            } else {
                 echo json_encode(['success' => false, 'message' => 'Error al guardar el cliente en la base de datos.']);
            }
        }
        exit();
    }
}
