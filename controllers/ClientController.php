<?php
require_once '../models/Client.php';
<<<<<<< HEAD
require_once '../models/Order.php';
=======
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317

class ClientController
{
    private $clientModel;
<<<<<<< HEAD
    private $orderModel;
=======
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317

    public function __construct($db_connection)
    {
        $this->clientModel = new Client($db_connection);
<<<<<<< HEAD
        $this->orderModel = new Order($db_connection);
=======
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
<<<<<<< HEAD
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'fecha' => $_GET['fecha'] ?? ''
        ];

        $clients = $this->clientModel->findAllWithFilters($filters);
=======
        $clients = $this->clientModel->findAll();
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/index.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    public function show($id) {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        
        $client = $this->clientModel->findById($id);
        
        if ($client) {
            $client['total_pedidos'] = $this->clientModel->countOrders($id);
            $client['pedidos'] = $this->orderModel->findByClientId($id); 
        }

        require_once '../views/layouts/header.php';
        require_once '../views/pages/clients/show.php';
        require_once '../views/layouts/footer.php';
    }

=======
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
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
        header('Location: /sistemagestion/clients');
        exit();
    }

<<<<<<< HEAD
=======
    // --- MÉTODOS AHORA PROTEGIDOS ---
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
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
        header('Location: /sistemagestion/clients');
        exit();
    }

    public function delete($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/clients');
            exit();
        }
        $this->clientModel->delete($id);
        header('Location: /sistemagestion/clients');
        exit();
    }
<<<<<<< HEAD
=======
    // --- FIN DE MÉTODOS PROTEGIDOS ---
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317

    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['term'])) {
            $clients = $this->clientModel->searchByTerm($_GET['term']);
            header('Content-Type: application/json');
            echo json_encode($clients);
            exit();
        }
    }
}