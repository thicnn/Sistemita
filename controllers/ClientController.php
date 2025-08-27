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

    public function index()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        
        $filters = ['search' => $_GET['search'] ?? ''];
        
        // --- Lógica de Paginación ---
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $perPage = 6; // 6 clientes por página para el diseño de tarjetas

        $totalClients = $this->clientModel->countAllWithFilters($filters);
        $totalPages = ceil($totalClients / $perPage);
        $offset = ($page - 1) * $perPage;

        $clients = $this->clientModel->findAllWithFilters($filters, $perPage, $offset);
        
        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function show($id) {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        
        $client = $this->clientModel->findById($id);
        
        if (!$client) {
            // Manejar cliente no encontrado
            header("HTTP/1.0 404 Not Found");
            echo "<h1>Error 404: Cliente no encontrado</h1>";
            exit();
        }

        // Obtenemos el historial de pedidos del cliente
        $client['pedidos'] = $this->orderModel->findByClientId($id); 
        
        // Calculamos algunas estadísticas
        $client['total_gastado'] = array_sum(array_column($client['pedidos'], 'costo_total'));
        $client['total_pedidos'] = count($client['pedidos']);

        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/show.php';
        require_once '../views/layouts/footer.php';
    }


    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        $this->clientModel->create($_POST['nombre'], $_POST['telefono'], $_POST['email'], $_POST['notas']);
        
        // Crear notificación toast
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
        
        // Crear notificación toast
        $_SESSION['toast'] = ['message' => '¡Cliente actualizado correctamente!', 'type' => 'info'];

        header('Location: /sistemagestion/clients');
        exit();
    }

    public function delete($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/clients');
            exit();
        }
        $this->clientModel->delete($id);

        // Crear notificación toast
        $_SESSION['toast'] = ['message' => 'Cliente eliminado.', 'type' => 'danger'];
        
        header('Location: /sistemagestion/clients');
        exit();
    }

    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['term'])) {
            $clients = $this->clientModel->searchByTerm($_GET['term']);
            header('Content-Type: application/json');
            echo json_encode($clients);
            exit();
        }
    }
}