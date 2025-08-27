<?php
require_once '../models/Order.php';
require_once '../models/Client.php';
require_once '../models/Product.php';

class OrderController
{
    private $orderModel;
    private $clientModel;
    private $productModel;

    public function __construct($db_connection)
    {
        $this->orderModel = new Order($db_connection);
        $this->clientModel = new Client($db_connection);
        $this->productModel = new Product($db_connection);
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        
        $filters = [
            'search' => $_GET['search'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? '',
            'sort' => $_GET['sort'] ?? 'fecha_creacion',
            'dir' => $_GET['dir'] ?? 'desc'
        ];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $perPage = 15;

        $totalOrders = $this->orderModel->countAllWithFilters($filters);
        $totalPages = ceil($totalOrders / $perPage);
        $offset = ($page - 1) * $perPage;

        $orders = $this->orderModel->findAllWithFilters($filters, $perPage, $offset);
        
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function showCreateForm()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        
        // ¡ESTA LÍNEA AHORA FUNCIONARÁ PERFECTAMENTE!
        $clients = $this->clientModel->findAll(); 
        
        $products = $this->productModel->findAllAvailable();
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/create.php';
        require_once '../views/layouts/footer.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $es_interno = isset($_POST['es_interno']) ? 1 : 0;
            $success = $this->orderModel->create(
                $_POST['cliente_id'],
                $_SESSION['user_id'],
                $_POST['estado'],
                $_POST['notas'],
                $_POST['items'] ?? [],
                $es_interno
            );
            
            $_SESSION['toast'] = $success 
                ? ['message' => '¡Pedido creado con éxito!', 'type' => 'success']
                : ['message' => 'Error al crear el pedido.', 'type' => 'danger'];

            header('Location: /sistemagestion/orders');
            exit();
        }
    }

    public function show($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) { echo "Pedido no encontrado."; exit(); }
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/show.php';
        require_once '../views/layouts/footer.php';
    }

    public function addPayment($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order = $this->orderModel->findByIdWithDetails($id);
            if ($order) {
                $totalPagado = array_sum(array_column($order['pagos'], 'monto'));
                $saldoPendiente = $order['costo_total'] - $totalPagado;
                $monto = (float)$_POST['monto'];
                if (!empty($monto) && is_numeric($monto) && $monto > 0 && $monto <= $saldoPendiente) {
                    $this->orderModel->addPayment($id, $monto, $_POST['metodo_pago']);
                    $_SESSION['toast'] = ['message' => '¡Pago registrado correctamente!', 'type' => 'success'];
                } else {
                    $_SESSION['toast'] = ['message' => 'Monto de pago inválido.', 'type' => 'danger'];
                }
            }
            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }

    public function showEditForm($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        $order = $this->orderModel->findByIdWithDetails($id);
        if (!$order) { echo "Pedido no encontrado."; exit(); }
        require_once '../views/layouts/header.php';
        require_once '../views/pages/orders/edit.php';
        require_once '../views/layouts/footer.php';
    }

    public function update($id)
    {
        if (!isset($_SESSION['user_id'])) { header('Location: /sistemagestion/login'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevoEstado = $_POST['estado'] ?? $this->orderModel->findByIdWithDetails($id)['estado'];

            if ($nuevoEstado === 'Entregado') {
                $this->orderModel->settlePayment($id);
            }
            
            $motivo_cancelacion = ($nuevoEstado === 'Cancelado' && !empty($_POST['motivo_cancelacion'])) ? $_POST['motivo_cancelacion'] : null;
            $es_interno = isset($_POST['es_interno']) ? 1 : 0;
            
            $this->orderModel->update($id, $nuevoEstado, $_POST['notas'], $motivo_cancelacion, $es_interno);

            $_SESSION['toast'] = ['message' => '¡Pedido actualizado!', 'type' => 'info'];
            
            header('Location: /sistemagestion/orders/show/' . $id);
            exit();
        }
    }
}