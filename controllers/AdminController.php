<?php
require_once '../models/Client.php';
require_once '../models/Order.php';
require_once '../models/Report.php';
require_once '../models/Product.php';
require_once '../models/Config.php';
require_once '../models/Material.php';

class AdminController {
    private $clientModel;
    private $orderModel;
    private $reportModel;
    private $productModel;
    private $configModel;
    private $materialModel;

    public function __construct($db_connection) {
        $this->clientModel = new Client($db_connection);
        $this->orderModel = new Order($db_connection);
        $this->reportModel = new Report($db_connection);
        $this->productModel = new Product($db_connection);
        $this->configModel = new Config($db_connection);
        $this->materialModel = new Material($db_connection);
    }

    private function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administrador') {
            header('Location: /sistemagestion/dashboard');
            exit();
        }
    }

    public function settings() {
        $this->checkAdmin();
        
        $data = [
            'meta_ventas_mensual' => $this->configModel->findByKey('goal', 'meta_ventas_mensual'),
            'meta_pedidos_mensual' => $this->configModel->findByKey('goal', 'meta_pedidos_mensual')
        ];

        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/settings.php';
        require_once '../views/layouts/footer.php';
    }

    public function updateGoals() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['meta_ventas_mensual'])) {
                $this->configModel->updateOrCreate('goal', 'meta_ventas_mensual', $_POST['meta_ventas_mensual']);
            }
            if (isset($_POST['meta_pedidos_mensual'])) {
                $this->configModel->updateOrCreate('goal', 'meta_pedidos_mensual', $_POST['meta_pedidos_mensual']);
            }
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Metas actualizadas correctamente.'];
        }
        header('Location: /sistemagestion/admin/settings');
        exit();
    }

    public function storeSetting() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->configModel->create($_POST['tipo'], $_POST['nombre'], $_POST['valor']);
        }
        header('Location: /sistemagestion/admin/settings');
        exit();
    }

    // --- GESTIÓN DE PRODUCTOS ---
    public function listProducts() {
        $this->checkAdmin();
        $filters = ['search' => $_GET['search'] ?? '', 'tipo' => $_GET['tipo'] ?? ''];
        $products = $this->productModel->searchAndFilter($filters);
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($products);
            exit();
        }

        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/products.php';
        require_once '../views/layouts/footer.php';
    }

    public function showProductCreateForm() {
        $this->checkAdmin();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/create_product.php';
        require_once '../views/layouts/footer.php';
    }

    public function storeProduct() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->create($_POST['tipo'], $_POST['categoria'] ?? '', $_POST['descripcion'], $_POST['precio']);
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }

    public function showProductEditForm($id) {
        $this->checkAdmin();
        $product = $this->productModel->findById($id);
        if (!$product) {
            header('Location: /sistemagestion/admin/products');
            exit();
        }

        $data = [
            'product' => $product,
            'associated_materials' => $this->productModel->getMaterialesAsociados($id),
            'all_materials' => $this->materialModel->findAll()
        ];

        require_once '../views/layouts/header.php';
        require_once '../views/pages/admin/edit_product.php';
        require_once '../views/layouts/footer.php';
    }

    public function updateProduct($id) {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponible = isset($_POST['disponible']) ? 1 : 0;
            $this->productModel->update($id, $_POST['descripcion'], $_POST['precio'], $disponible);
        }
        header('Location: /sistemagestion/admin/products');
        exit();
    }
    
    public function deleteProduct($id) {
        $this->checkAdmin();
        $this->productModel->delete($id);
        header('Location: /sistemagestion/admin/products');
        exit();
    }

    // --- GESTIÓN DE MATERIALES POR PRODUCTO ---
    public function addMaterialToProduct($product_id) {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['material_id'], $_POST['cantidad_consumida'])) {
            $this->productModel->asociarMaterial($product_id, $_POST['material_id'], $_POST['cantidad_consumida']);
        }
        header('Location: /sistemagestion/admin/products/edit/' . $product_id);
        exit();
    }

    public function removeMaterialFromProduct($asociacion_id) {
        $this->checkAdmin();
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id > 0) {
            $this->productModel->desasociarMaterial($asociacion_id);
        }
        header('Location: /sistemagestion/admin/products/edit/' . $product_id);
        exit();
    }

    // --- ACCIONES AVANZADAS DE BORRADO ---
    public function deleteData() {
        $this->checkAdmin();
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