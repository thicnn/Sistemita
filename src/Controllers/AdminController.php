<?php
namespace App\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Report;
use App\Models\Product;
use App\Models\Config;

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
        
        $papeles = $this->configModel->findByType('papel');
        $acabados = $this->configModel->findByType('acabado');

        require_once BASE_PATH . '/views/layouts/header.php';
        require_once BASE_PATH . '/views/pages/admin/settings.php';
        require_once BASE_PATH . '/views/layouts/footer.php';
    }

    public function storeSetting() {
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
    // --- GESTIÓN DE PRODUCTOS ---
    public function listProducts() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        $filters = ['search' => $_GET['search'] ?? '', 'tipo' => $_GET['tipo'] ?? ''];
        $products = $this->productModel->searchAndFilter($filters);
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($products);
            exit();
        }

        require_once BASE_PATH . '/views/layouts/header.php';
        require_once BASE_PATH . '/views/pages/admin/products.php';
        require_once BASE_PATH . '/views/layouts/footer.php';
    }

    public function showProductCreateForm() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        require_once BASE_PATH . '/views/layouts/header.php';
        require_once BASE_PATH . '/views/pages/admin/create_product.php';
        require_once BASE_PATH . '/views/layouts/footer.php';
    }

    public function storeProduct() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->create($_POST['tipo'], $_POST['categoria'] ?? '', $_POST['descripcion'], $_POST['precio']);
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }

    public function showProductEditForm($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        $product = $this->productModel->findById($id);
        require_once BASE_PATH . '/views/layouts/header.php';
        require_once BASE_PATH . '/views/pages/admin/edit_product.php';
        require_once BASE_PATH . '/views/layouts/footer.php';
    }

    public function updateProduct($id) {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') { header('Location: /sistemagestion/dashboard'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            $this->productModel->update($id, $_POST['descripcion'], $_POST['precio'], $disponible);
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }
    
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