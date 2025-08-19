<?php
<<<<<<< HEAD
require_once '../models/Client.php';
require_once '../models/Order.php';
require_once '../models/Report.php';
require_once '../models/Product.php';
require_once '../models/Config.php';

class AdminController {
    private $clientModel;
    private $orderModel;
    private $reportModel;
    private $productModel;
    private $configModel;

    public function __construct($db_connection) {
        $this->clientModel = new Client($db_connection);
        $this->orderModel = new Order($db_connection);
        $this->reportModel = new Report($db_connection);
        $this->productModel = new Product($db_connection);
        $this->configModel = new Config($db_connection);
    }

    // Muestra el panel principal de Ajustes/Configuración
    public function settings() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
=======
require_once '../models/Config.php';
require_once '../models/Product.php';

class AdminController
{
    private $configModel;
    private $productModel;

    public function __construct($db_connection)
    {
        $this->configModel = new Config($db_connection);
        $this->productModel = new Product($db_connection);
    }

    public function settings()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        $papeles = $this->configModel->findByType('papel');
        $acabados = $this->configModel->findByType('acabado');
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/settings.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    // --- GESTIÓN DE PRODUCTOS ---
    public function listProducts() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        $filters = ['search' => $_GET['search'] ?? '', 'tipo' => $_GET['tipo'] ?? ''];
        $products = $this->productModel->searchAndFilter($filters);
        
=======
    public function storeSetting()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->configModel->create($_POST['tipo'], $_POST['nombre'], $_POST['valor']);
        }
        header('Location: /sistemagestion/admin/settings');
        exit();
    }

    public function listProducts()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        $filters = ['search' => $_GET['search'] ?? null, 'tipo' => $_GET['tipo'] ?? null];
        $products = $this->productModel->searchAndFilter($filters);
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($products);
            exit();
        }
<<<<<<< HEAD

=======
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/products.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    public function showProductCreateForm() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
=======
    public function showProductCreateForm()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/create_product.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    public function storeProduct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->create($_POST['tipo'], $_POST['categoria'] ?? '', $_POST['descripcion'], $_POST['precio']);
=======
    public function storeProduct()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->create(
                $_POST['tipo'],
                $_POST['categoria'] ?? '',
                $_POST['descripcion'],
                $_POST['precio']
            );
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }

<<<<<<< HEAD
    public function showProductEditForm($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
=======
    public function showProductEditForm($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        $product = $this->productModel->findById($id);
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/edit_product.php';
        require_once '../views/layouts/footer.php';
    }

<<<<<<< HEAD
    public function updateProduct($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
=======
    public function updateProduct($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            $this->productModel->update($id, $_POST['descripcion'], $_POST['precio'], $disponible);
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }
<<<<<<< HEAD
    
    public function deleteProduct($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        $this->productModel->delete($id);
        header('Location: /sistemagestion/admin/products');
        exit();
    }

    // --- ACCIONES AVANZADAS DE BORRADO ---
    public function deleteData() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
            switch ($_POST['type']) {
                case 'clients':
                    $this->clientModel->deleteAll();
                    break;
                case 'orders':
                    $this->orderModel->deleteAll();
                    break;
                case 'counters':
                    $this->reportModel->deleteAllCounters();
                    break;
                case 'provider_payments':
                    $this->reportModel->deleteAllProviderPayments();
                    break;
            }
        }
        header('Location: /sistemagestion/admin/settings');
        exit();
    }
}
=======
    /**
     * ¡NUEVO! Procesa la eliminación de un producto.
     */
    public function deleteProduct($id)
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
        $this->productModel->delete($id);
        // Redirigimos de vuelta a la lista de productos
        header('Location: /sistemagestion/admin/products');
        exit();
    }
}
>>>>>>> d1e912453c5dcfd0af21d9fc4c6650aa3443e317
